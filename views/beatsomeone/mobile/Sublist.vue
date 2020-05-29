<template>

    <div class="wrapper">

        <Header :is-login="isLogin"/>
        <div class="container sub">
            <div class="sublist">
                <!-- 필터 -->
                <div class="sublist__filter" v-show="isShowFilter">
                    <div class="sublist__filter-content">
                        <div class="row">
                            <div class="filter">
                                <h2 class="filter__title">{{ $t('filter') }}</h2>
                                <div class="filter__content">
                                    <ul class="filter__list">
                                        <li class="filter__item" v-for="(f,index) in listFilter" :key="index">
                                            <label :for="'fillter1'+index" class="checkbox">
                                                <input
                                                        type="radio"
                                                        name="filter"
                                                        hidden
                                                        :id="'fillter1'+index"
                                                        :value="f"
                                                        v-model="param.currentGenre"
                                                />
                                                <span></span> {{ listFilterName[index] }}
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="filter">
                                <h2 class="filter__title">{{ $t('subGenres') }}</h2>
                                <div class="filter__content">
                                    <ul class="filter__list">
                                        <li class="filter__item" v-for="(f,index) in listSubgenres" :key="index">
                                            <label :for="'fillter2'+index" class="checkbox">
                                                <input
                                                        type="radio"
                                                        name="subgenres"
                                                        hidden
                                                        :id="'fillter2'+index"
                                                        :value="f"
                                                        v-model="param.currentSubgenres"
                                                />
                                                <span></span> {{ listSubgenresName[index] }}
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="filter">
                                <h2 class="filter__title">BPM</h2>
                                <div class="filter__content">
                                    <div class="bpmRange">
                                        <input type="text" />
                                    </div>
                                    <div class="bpmRangeInfo">
                                        <input type="text" readonly id="bpm-start"/>
                                        <span> - </span>
                                        <input type="text" readonly id="bpm-end" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="filter">
                                <h2 class="filter__title folded">{{ $t('moods') }}</h2>
                                <div class="filter__content" style="display: none;">
                                    <ul class="filter__list">
                                        <li class="filter__item" v-for="(f,index) in listMoods" :key="index">
                                            <label :for="'fillter3'+index" class="checkbox">
                                                <input
                                                        type="radio"
                                                        name="moods"
                                                        hidden
                                                        :id="'fillter3'+index"
                                                        :value="f"
                                                        v-model="param.currentMoods"
                                                />
                                                <span></span> {{ listMoodsName[index] }}
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="filter">
                                <h2 class="filter__title folded">{{ $t('trackType') }}</h2>
                                <div class="filter__content" style="display: none;">
                                    <ul class="filter__list">
                                        <li class="filter__item" v-for="(f,index) in listTrackType" :key="index">
                                            <label :for="'fillter4'+index" class="checkbox">
                                                <input
                                                        type="radio"
                                                        name="trackTypes"
                                                        hidden
                                                        :id="'fillter4'+index"
                                                        :value="f"
                                                        v-model="param.currentTrackType"
                                                />
                                                <span></span> {{ listTrackTypeName[index] }}
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="showFilter" @click="toggleFilter"></button>
                <!-- 필터 끝 -->
                <div class="sublist__content">
                    <div class="row">
                        <h2 class="section-title" v-if="param.search">
                            <div class="wrap">{{ $t('searchResultsFor') }} '{{ param.search }}'</div>
                        </h2>
                        <h2 class="section-title">
                            <div class="wrap">TOP <span class="number">5</span></div>
                        </h2>
                        <div class="topFive">
                            <div class="topFice__slider">
                                <div class="trending__slide-item albumItem" v-for="(i,index) in listTop5" :key="index" @click="selectItem(i)">
                                    <button class="albumItem__cover">
                                        <img :src="'/uploads/cmallitem/' + i.cit_file_1" :alt="i.cit_name" />
                                    </button>
                                    <a class="albumItem__link">
                                        <h4 class="albumItem__title">{{ i.cit_name }}</h4>
                                        <p class="albumItem__singer">{{ i.musician }}</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="playList"  v-infinite-scroll="loading" infinite-scroll-immediate-check="false">
                            <transition-group
                                    name="staggered-fade"
                                    tag="ul"
                                    v-bind:css="false"
                                    v-on:before-enter="beforeEnter"
                                    v-on:enter="enter"
                                    v-on:leave="leave">
                                <template v-for="item in list"  >
                                    <KeepAliveGlobal :key="item.cit_key">
                                        <Index_Items :item="item" :key="item.cit_key"></Index_Items>
                                    </KeepAliveGlobal>
                                </template>
                            </transition-group>
                            <div v-if="busy">
                                <Loader key="loader" ></Loader>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <main-player></main-player>
        <Footer/>
    </div>
</template>

<script>

    import $ from "jquery";
    require('@/assets_m/js/function');
    import Header from "./include/Header";
    import Footer from "./include/Footer";
    import Index_Items from "./Index_Items";
    import { EventBus } from '*/src/eventbus';
    import Velocity from "velocity-animate";
    import Loader from '*/vue/common/Loader';
    import MainPlayer from "@/vue/common/MobileMainPlayer";
    import KeepAliveGlobal from 'vue-keep-alive-global';

    export default {
        components: {Header,Footer,Index_Items,Loader,MainPlayer,KeepAliveGlobal},
        data: function() {
            return {
                slick: null,
                isShowFilter: false,
                isLogin: false,
                listFilter: ['All Genre'].concat(window.genre).concat(['Free Beats']),
                listSubgenres: ['All'].concat(window.genre).concat(['Free Beats']),
                listMoods: ['All'].concat(window.moods),
                listTrackType: ['All types'].concat(window.trackType),
                offset: 0,
                last_offset: 0,
                list: null,
                listTop5: null,
                param: {
                    currentGenre: null,
                    currentSubgenres : null,
                    currentMoods: null,
                    currentTrackType: null,
                    currentBpmFr: 0,
                    currentBpmTo : 120,
                },
                busy: false,
            }
        },
        watch: {
            // 검색조건 변경
            param: {
                deep: true,
                handler(n,o) {
                    // log.debug({
                    //     'change param' : n,
                    // });
                    if(o) {
                        this.updateAllList();
                    }
                }
            },
        },
        created() {
            this.param.currentGenre = this.listFilter[0];
            this.param.currentSubgenres = this.listSubgenres[0];
            this.param.currentMoods = this.listMoods[0];
            this.param.currentTrackType = this.listTrackType[0];
        },

        mounted() {
            $(".filter__title").on("click", function() {
                $(this).toggleClass("folded");
                $(this)
                    .siblings(".filter__content")
                    .stop()
                    .slideToggle();
            });

            $('.player__util-toggle-btn').on('click', function(){
                $(this).toggleClass('js-active');
                $('.player__util').toggleClass('js-active');
            });
            $(".filter__title").on("click", function() {
                $(this).toggleClass("folded");
                $(this)
                    .siblings(".filter__content")
                    .stop()
                    .slideToggle();
            });

            // BPM range
            if ($(".bpmRange").length) {
                $(".bpmRange input").ionRangeSlider({
                    skin: "round",
                    type: "double",
                    min: 0,
                    max: 170,
                    from: 0,
                    to: 125,
                    onStart: (data) => {

                        $("#bpm-start").val(0);
                        $("#bpm-end").val(125);
                        this.param.currentBpmFr = data.from_pretty;
                        this.param.currentBpmTo = data.to_pretty;
                    },
                    onChange: (data) => {
                        // log.debug({
                        //     'rpm onChange':data,
                        // })
                        $("#bpm-start").val(data.from_pretty);
                        $("#bpm-end").val(data.to_pretty);
                        this.param.currentBpmFr = data.from_pretty;
                        this.param.currentBpmTo = data.to_pretty;
                    }
                });
            }

            // 커스텀 셀렉트 옵션
            $(".custom-select").on("click", function() {
                $(this)
                    .siblings(".custom-select")
                    .removeClass("active")
                    .find(".options")
                    .hide();
                $(this).toggleClass("active");
                $(this)
                    .find(".options")
                    .toggle();
            });

            this.updateAllList();

        },
        computed: {
            listFilterName() {
                let list = [],
                    _self = this

                this.listFilter.forEach(function (val) {
                    list.push(_self.$t('genre' + val.replace(/ /g,"")))
                })

                return list
            },
            listSubgenresName() {
                let list = [],
                    _self = this

                this.listSubgenres.forEach(function (val) {
                    list.push(_self.$t('genre' + val.replace(/ /g,"")))
                })

                return list
            },
            listMoodsName() {
                let list = [],
                    _self = this

                this.listMoods.forEach(function (val) {
                    list.push(_self.$t('moods' + val.replace(/ /g,"")))
                })

                return list
            },
            listTrackTypeName() {
                let list = [],
                    _self = this

                this.listTrackType.forEach(function (val) {
                    list.push(_self.$t('trackType' + val.replace(/ /g,"")))
                })

                return list
            }
        },
        methods: {
            loading() {
                if(this.busy) return;
                if(this.last_offset === this.offset) return;
                this.busy = true;
                this.getListMore();
            },
            updateAllList:  _.debounce(function() {
                this.getList();
                this.getTopList();
            },100),
            doSlide() {
                this.slick = $(".topFive .topFice__slider").slick({
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    swipeToSlide: true,
                    autoplay: true,
                    autoplaySpeed: 2000,
                    centerMode: true,
                    centerPadding: "25px",
                    arrows: false,
                    dots: true
                });
            },
            removeSlide() {
                this.slick.slick('unslick');
            },
            toggleFilter() {
                this.isShowFilter = !this.isShowFilter;

            },
            addCart() {

                let detail_qty = {};
                detail_qty[this.item['cde_id']] = 1;
                Http.post( `/beatsomeoneApi/itemAction`,{stype: 'cart',cit_id:this.item.cit_id,chk_detail:[this.item.cde_id],detail_qty:detail_qty,}).then(r=> {
                    if(!r) {
                        log.debug('장바구니 담기 실패');
                    } else {
                        EventBus.$emit('add_cart');
                        log.debug('장바구니 담기 성공');

                    }
                });
            },
            selectItem(i) {
                const path = `/beatsomeone/detail/${i.cit_key}`;
                window.location.href = path;
            },
            getList() {
                const p = {
                    limit: 10,
                    offset: 0,
                    sort: this.param.sort,
                    genre: this.param.currentGenre,
                    subgenre: this.param.currentSubgenres,
                    bpmFr: this.param.currentBpmFr,
                    bpmTo: this.param.currentBpmTo,
                    moods: this.param.currentMoods,
                    trackType: this.param.currentTrackType,
                    search: this.param.search
                }
                Http.post(`/beatsomeoneApi/sublist_list`,p).then(r=> {
                    this.list = r;
                    this.offset = this.list.length;
                });
            },
            getListMore: _.debounce(function() {

                //if(this.busy) return;
                this.busy = true;
                const p = {
                    limit: 10,
                    offset: this.offset,
                    sort: this.param.sort,
                    genre: this.param.currentGenre,
                    subgenre: this.param.currentSubgenres,
                    bpmFr: this.param.currentBpmFr,
                    bpmTo: this.param.currentBpmTo,
                    moods: this.param.currentMoods,
                    trackType: this.param.currentTrackType,
                    search: this.param.search
                }
                Http.post(`/beatsomeoneApi/sublist_list`,p).then(r=> {
                    this.list = this.list.concat(r);
                    this.last_offset = this.offset;
                    this.offset = this.list.length;
                    this.busy = false;
                });
            },1000),
            getTopList() {
                const p = {
                    genre: this.param.currentGenre,
                    subgenre: this.param.currentSubgenres,
                    bpmFr: this.param.currentBpmFr,
                    bpmTo: this.param.currentBpmTo,
                    moods: this.param.currentMoods,
                    trackType: this.param.currentTrackType,
                    limit: 5
                }
                Http.post(`/beatsomeoneApi/sublist_top_list`,p).then(r=> {

                    if(this.slick) {
                        this.removeSlide();
                    }

                    this.listTop5 = r;

                    this.$nextTick(function() {
                        this.doSlide();
                    });
                });
            },

            beforeEnter: function (el) {
                el.style.opacity = 0
                el.style.height = 0
            },
            enter: function (el, done) {
                var delay = el.dataset.index * 150
                setTimeout(function () {
                    Velocity(
                        el,
                        { opacity: 1, height: 55, 'margin-bottom': 1,  },
                        { complete: done }
                    )
                }, delay)
            },
            leave: function (el, done) {
                var delay = el.dataset.index * 150
                setTimeout(function () {
                    Velocity(
                        el,
                        { opacity: 0, height: 0, 'margin-bottom': 0,  },
                        { complete: done }
                    )
                }, delay)
            }
        },

    }

</script>

<style lang="scss">
    @import '@/assets_m/scss/App.scss';
</style>

<style lang="css">
    @import '/assets/plugins/slick/slick.css';
    @import '/assets/plugins/rangeSlider/css/ion.rangeSlider.min.css';


</style>
