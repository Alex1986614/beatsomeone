<template>
    <li v-if="item" class="playList__itembox" :id="'playList__item'+ item.cit_id">
        <div class="playList__item playList__item--title">
            <div class="col favorite" :class="{active : item.is_wish === '1' }" @click="toggleWish" v-if="!hideFav">
                <button>{{ $t('favorite') }}</button>
            </div>
            <div class="col favorite" v-if="showCheck">
                <label class="checkbox nfavorites__checkbox">
                    <input type="checkbox" hidden v-model="checked">
                    <span></span>
                </label>
            </div>

            <div class="col name">
                <figure>
                    <span class="playList__cover"><img :src="'/uploads/cmallitem/' + item.thumb" alt/><i class="label new" v-show="item.is_new === true">N</i></span>
                    <figcaption class="pointer" @click="selectItem(item)">
                        <h3 class="playList__title">{{ truncate(item.cit_name, 30) }}</h3>
                        <span class="playList__by">by {{ item.mem_nickname }}</span>
                    </figcaption>
                </figure>

                <div class="tags">
                    <div>
                        <!-- <button style="color:#3873d3;" v-if="item.cit_freebeat === '1'">{{ $t('lang1') }}</button> -->
                        <!-- <button style="color:#ffda2a;" v-if="item.cit_org_content === '1'">{{ $t('lang2') }}</button> -->
                        <img style="margin-left: 5px;" v-if="item.cit_org_content === '1'" src="/assets/images/icon/list_original.png"/> 
                        <img style="margin-left: 5px;" v-if="item.cit_org_content === '1'" src="/assets/images/icon/list_original.png"/> 
                        <img style="margin-left: 5px;" v-if="item.cit_org_content === '1'" src="/assets/images/icon/list_original.png"/> 
                        <img style="margin-left: 5px;" v-if="item.cit_org_content === '1'" src="/assets/images/icon/list_original.png"/> 
                        <img style="margin-left: 5px;" v-if="item.cit_org_content === '1'" src="/assets/images/icon/list_original.png"/> 
                    </div>
                    <!-- <div>
                        <button style="color:#fff;" v-if="item.cit_officially_registered === '1'">{{ $t('lang3') }}
                        </button>
                    </div> -->
                </div>
                <!-- 서브리스트 토글 버튼 -->
                <button class="toggle-subList" v-if="item.subPlayList && item.subPlayList.length > 0"></button>

            </div>

            <div class="col genre">
              <span v-for="(t,i) in hashtag" :key="i" v-if="t.trim()"><button @click="clickHash(t)" v-hover="'active'">{{ truncate(t, 15) }}</button></span>
            </div>
            <div class="col playbtn">
                <button class="btn-play" @click="playAudio(item)" :data-action="'playAction' + item.cit_id ">재생</button>
                <span class="timer"><span class="current">0:00 /</span><span class="duration">0:00</span></span>
            </div>
            <div class="col spectrum">
                <div class="wave"></div>
            </div>
            <div class="col buybtn">
                <button @click="addCart">{{ $t('lang106') }}</button>
            </div>
            <div class="col utils" v-if="false">
                <a :href="`/cmallact/download_sample/${item.cde_id}`" class="download">{{ $t('download') }}</a>
            </div>
            <div class="col more_shared">
                <button>
                    {{ $t('share') }}
                    <span class="tooltip">
                      <a @click="clickShare('twitter')">{{ $t('lang107') }}</a><a @click="clickShare('facebook')">{{ $t('lang108') }}</a><a @click="copyLinkToClipboard()">{{ $t('lang109') }}</a>
                    </span>
                </button>
            </div>
        </div>
        <PurchaseTypeSelector :purchaseTypeSelectorPopup.sync="purchaseTypeSelectorPopup"
                              :item="item"></PurchaseTypeSelector>
    </li>
</template>

<script>
import {EventBus} from "*/src/eventbus";
import $ from "jquery";
import WaveSurfer from "wavesurfer.js";
import PurchaseTypeSelector from "./component/PurchaseTypeSelector";

export default {
        components: {
            PurchaseTypeSelector,
        },
        props: [
            "item",
            "hideFav",
            "showCheck",
            "value",
            'disabledWave'
        ],
        data: function () {
            return {
                isOpenSubmenu: false,
                ws: null,
                isPlay: false,
                isReady: false,
                purchaseTypeSelectorPopup: false,
            };
        },
        computed: {
            hashtag() {
                return this.item.hashTag ? this.item.hashTag.split(",") : "";
            },
            checked: {
                get() {
                    return this.value;
                },
                set(val) {
                    this.$emit('input', val);
                }
            }
        },
        directives: {
            hover: {
                bind(el, binding, vnode) {
                    const {value = ""} = binding;
                    el.addEventListener("mouseenter", () => {
                        el.classList.add(value);
                    });
                    el.addEventListener("mouseleave", () => {
                        el.classList.remove(value);
                    });
                },
                unbind(el, binding, vnode) {
                    el.removeEventListener("mouseenter");
                    el.removeEventListener("mouseleave");
                },
            },
        },
        mounted() {
            EventBus.$on("index_items_open_submenu", (r) => {
                if (this._uid !== r) {
                    this.isOpenSubmenu = false;
                }
            });
            EventBus.$on("player_request_start", (r) => {
                log.debug({
                    "ON ITEM: player_request_start": r,
                    R: this.item.cit_id === r.item.cit_id,
                    "this.item.cit_id": this.item.cit_id,
                });
                if (this._uid != r.item._uid) {
                    this.stop();
                }
            });
            // 메인 플레이어 재생 시작
            EventBus.$on("main_player_play", (r) => {
                log.debug({
                    _uid: this._uid,
                    "R _uid": r._uid,
                    EQ: this._uid == r._uid,
                });
                if (this._uid == r._uid) {
                    this.start();
                } else {
                    this.stop();
                }
            });
            // 메인 플레이어 재생 종료
            EventBus.$on("main_player_stop", (r) => {
                log.debug({
                    "ON ITEM: main_player_stop": r,
                    R: this.item.cit_id === r.item.id,
                    "this.item.cit_id": this.item.cit_id,
                });
                if (this._uid === r._uid) {
                    this.stop();
                }
            });
            if (this.item.cit_type3 === '0') {
                this.$set(this.item, 'is_new', false);
                let now = new Date();
                let startDateTime = new Date(this.item.cit_start_datetime);
                if ((now - startDateTime) < 1000 * 3600 * 24 * 7) this.$set(this.item, 'is_new', true);
            } else if (this.item.cit_type3 === '1') {
                this.$set(this.item, 'is_new', true);
            }
            if (!this.disabledWave && !this.ws) {
                this.setAudioInstance(this.item);
            }
        },
        methods: {
            truncate(str, n) {
                if (this.byteSize(str) === str.length) { // 영문
                    return (str.length > n) ? str.substr(0, n-1) + '...' : str;
                } else { // 한글
                    return (this.byteSize(str) / 3 > n / 2) ? str.substr(0, n / 2 - 1) + '...' : str;
                }
            },
            byteSize(str) {
                return new Blob([str]).size;
            },
            stop() {
                if (this.ws) {
                    this.ws.pause();
                }

                const el = $("#playList__item" + this.item.cit_id);
                el.removeClass("playing");
                this.isPlay = false;
            },
            start(isInit) {
                // log.debug('ITEM : start');
                if (this.ws) {
                    this.ws.play();
                }

                const el = $("#playList__item" + this.item.cit_id);
                el.addClass("playing");
                if (!isInit) {
                    this.isPlay = true;
                }
            },
            openSubmenu() {
                this.isOpenSubmenu = !this.isOpenSubmenu;
                EventBus.$emit("index_items_open_submenu", this._uid);
            },
            toggleWish() {
                Http.post(`/beatsomeoneApi/toggle_wish_item/${this.item.cit_id}`).then(
                    (r) => {
                        if (r === true) {
                            this.item.is_wish = this.item.is_wish === "1" ? "0" : "1";
                        }
                    }
                );
            },
            addCart() {
                // this.item.detail = {
                //     LEASE: {
                //         cde_id: this.item.cde_id || null,
                //         cde_price: this.item.cde_price || null,
                //         cde_price_d: this.item.cde_price_d || null,
                //     },
                //     STEM: {
                //         cde_id: this.item.cde_id_2 || null,
                //         cde_price: this.item.cde_price_2 || null,
                //         cde_price_d: this.item.cde_price_d_2 || null,
                //     },
                // };
                this.purchaseTypeSelectorPopup = true;
            },
            selectItem(i) {
                window.location.href = `/detail/${i.cit_key}`;
            },
            playAudio(i) {
                // 재생 시작
                if (!this.isPlay) {
                    if (!this.ws) {
                        this.setAudioInstance(this.item);
                    }

                    EventBus.$emit("player_request_start", {
                        _uid: this._uid,
                        item: this.item,
                        ws: this.ws,
                    });
                    this.start();
                }
                // 중지
                else {
                    EventBus.$emit("player_request_stop", {
                        _uid: this._uid,
                        item: this.item,
                        ws: this.ws,
                    });
                    this.stop();
                }
            },
            time_convert(num) {
                var minutes = Math.floor(num / 60);
                var seconds = num % 60;
                if (seconds < 10) {
                    seconds = "0" + seconds;
                }
                return minutes + ":" + seconds;
            },
            setAudioInstance(item) {
                this.ws = WaveSurfer.create({
                    container: "#playList__item" + item.cit_id + " .wave",
                    waveColor: "#696969",
                    progressColor: "#c3ac45",
                    hideScrollbar: true,
                    height: 40,
                    backgroundColor: "transparent",
                    drawingContextAttributes: {
                        desynchronized: false,
                    },
                });

                if (item.detail && item.detail.PREVIEW) {
                    this.ws.load(`/cmallact/download_sample/${item.detail.PREVIEW.cde_id}`, item.waveform, true);
                }

                this.ws.on("play", () => {
                    const el = document.querySelector("#playList__item" + this.item.cit_id);
                    if (el) {
                        el.classList.add("playing");
                    }
                    this.increaseMusicCount();
                });

                this.ws.on("audioprocess", (e) => {
                    const el = document.querySelector(
                        "#playList__item" + this.item.cit_id + " .current"
                    );
                    if (el) {
                        el.innerHTML = this.time_convert(parseInt(e, 10)) + " / ";
                    }
                });

                this.ws.on("ready", () => {
                    const el = document.querySelector(
                        "#playList__item" + this.item.cit_id + " .duration"
                    );
                    if (el) {
                        el.innerHTML = this.time_convert(parseInt(this.ws.getDuration(), 10));
                    }

                    if (this.isPlay) {
                        this.start(true);
                    }

                    this.isReady = true;
                });
                this.ws.on("pause", () => {
                });
            },
            //다운로드 증가
            increaseMusicCount() {
                Http.post(`/beatsomeoneApi/increase_music_count`, {
                    cde_id: this.item.cde_id,
                }).then((r) => {
                    if (!r) {
                        log.debug("카운트 증가 실패");
                    } else {
                        log.debug("카운트 증가 성공");
                    }
                });
            },
            // 해쉬 클릭
            clickHash(h) {
                window.location.href = `/beatsomeone/sublist?search=${h}`;
            },
            // 공유 클릭
            clickShare(sns) {
                Http.post(`/beatsomeoneApi/increase_item_share_count`, {
                    cit_id: this.item.cit_id,
                }).then((r) => {
                    if (!r) {
                        log.debug("공유 카운트 증가 실패");
                    } else {
                        log.debug("공유 카운트 증가 성공");
                    }
                });

                var url = `https://beatsomeone.com/detail/${this.item.cit_key}`;
                var txt = `${this.item.cit_name} / ${this.item.musician} / ${this.item.genre}`;

                var o;
                var _url = encodeURIComponent(url);
                var _txt = encodeURIComponent(txt);
                var _br = encodeURIComponent("\r\n");

                switch (sns) {
                    case "facebook":
                        o = {
                            method: "popup",
                            url: "http://www.facebook.com/sharer/sharer.php?u=" + _url,
                        };
                        break;

                    case "twitter":
                        o = {
                            method: "popup",
                            url:
                                "http://twitter.com/intent/tweet?text=" + _txt + "&url=" + _url,
                        };
                        break;

                    case "kakaostory":
                        o = {
                            method: "popup",
                            url: "https://story.kakao.com/share?url=" + _url,
                        };
                        break;

                    case "band":
                        o = {
                            method: "popup",
                            url: "http://www.band.us/plugin/share?body=" + _txt + _br + _url,
                        };
                        break;

                    default:
                        alert("지원하지 않는 SNS입니다.");
                        return false;
                }

                switch (o.method) {
                    case "popup":
                        window.open(
                            o.url,
                            "snspopup",
                            "width=500, height=400, menubar=no, status=no, toolbar=no"
                        );
                        break;

                    case "web2app":
                        if (navigator.userAgent.match(/android/i)) {
                            // Android
                            setTimeout(function () {
                                location.href =
                                    "intent://" + o.param + "#Intent;" + o.g_proto + ";end";
                            }, 100);
                        } else if (navigator.userAgent.match(/(iphone)|(ipod)|(ipad)/i)) {
                            // Apple
                            setTimeout(function () {
                                location.href = o.a_store;
                            }, 200);
                            setTimeout(function () {
                                location.href = o.a_proto + o.param;
                            }, 100);
                        } else {
                            alert("이 기능은 모바일에서만 사용할 수 있습니다.");
                        }
                        break;
                }
            },
            // 링크 복사
            copyLinkToClipboard() {
                var t = document.createElement("textarea");
                document.body.appendChild(t);
                t.value = `https://beatsomeone.com/detail/${this.item.cit_key}`;
                t.select();
                document.execCommand("copy");
                document.body.removeChild(t);
                alert(`복사되었습니다\nCtrl + V 를 눌러 확인해보세요`);
            },
        },
    };
</script>

<style lang="scss">
    .timer {
        margin-left: 20px !important;
        width: 63px;
    }

    .sublist .col.playbtn {
        flex: none;
    }

    .playList .playList__item .spectrum {
        width: 200px;
        flex: none;
    }

    .playList .playList__item .favorite {
        width: 70px;
        padding-left: 30px;
    }

    .playList .playList__item .name {
        flex: 1;
        width: auto;
        padding-left: 30px;

        figure {
            width: auto;
            flex: 1;
            width: calc(100% - 160px);

            figcaption {
                width: calc(100% - 85px);
            }
        }
    }

    .sublist {
        .col.name figure {
            min-width: auto;
        }

        .col.playbtn {
            width: 180px;
            flex: none;

            &:before {
                content: "";
                width: 1px;
                height: 14px;
                background: rgba(255, 255, 255, 0.1);
                display: block;
                position: absolute;
                top: 50%;
                bottom: auto;
                right: 0;
                -webkit-transform: translateY(-50%);
                transform: translateY(-50%);
            }
        }

        .col.spectrum {
            width: 0px !important;
            display: none !important;
        }

        .col.more_shared {
            width: 50px;
            flex: none;
        }

        .buybtn {
            width: 145px;
            flex: none;
        }
    }

    .playList .playList__item .buybtn button {
        white-space: nowrap;
    }
</style>
