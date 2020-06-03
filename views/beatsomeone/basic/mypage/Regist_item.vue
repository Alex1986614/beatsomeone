<template>
    <div class="wrapper">
        <Header :is-login="isLogin"/>
        <div class="container registered">
            <div class="registered__title">
                <h1>{{ $t('trackMetadataEditor') }}</h1>
            </div>
            <section class="registered__section">
                <div class="wrap">
                    <h2 class="registered__section-title">{{ $t('generalInfo') }}</h2>
                    <div class="registered__section-content">
                        <div class="row">
                            <div class="col">
                                <label class="form-item">
                                    <p class="form-title required">{{ $t('trackName') }}</p>
                                    <div class="input">
                                        <input type="text" v-model.trim="item.cit_name" :placeholder="$t('newTrack')" required/>
                                    </div>
                                    <span class="form-info">
                                        {{ $t('allowedCharLength') }}
                                    </span>
                                </label>

                                <label class="form-item">
                                    <p class="form-title">{{ $t('tags') }} (10)</p>
                                    <div class="input">
                                        <input type="text" v-model.trim="item.hashTag" :placeholder="$t('tagsSeparatedComma')" maxlength="200" id="tags" @input="chkHashTag"/>
                                        <span class="form-unit"><span ref="hashTagCount">0</span>/10</span>
                                    </div>
                                </label>

                                <div class="row">
                                    <div class="col">
                                        <label class="form-item">
                                            <p class="form-title required">{{ $t('trackType1') }}</p>
                                            <select v-model="item.trackType" class="custom-select-basic">
                                                <option value="">{{ $t('select') }}</option>
                                                <option v-for="(item, index) in listTrackType" :key="'trackType' + index" :value="item">{{ listTrackTypeName[index] }}</option>
                                            </select>
                                        </label>
                                    </div>
                                    <div class="col">
                                        <div class="form-item">
                                            <p class="form-title required">{{ $t('releaseDate') }}</p>
                                            <div class="input">
                                                <datetime
                                                        type="datetime"
                                                        v-model="item.cit_start_datetime"
                                                        :format="{ year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: '2-digit'}"
                                                        :phrases="{ok: 'Select', cancel: 'Exit'}"
                                                        :hour-step="1"
                                                        :minute-step="10"
                                                        :placeholder="$t('dateTime')"
                                                        :minDatetime="minReleaseDate"
                                                        value-zone="asia/Seoul"
                                                        class="release-date"
                                                        auto
                                                ></datetime>
                                            </div>
                                            <span class="form-info">
                                                {{ $t('releaseDateMsg') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-item">
                                    <p class="form-title">{{ $t('audiofilesForDownload') }}</p>
                                    <label for="unTaggedFile" class="addAudioFile waves-effect" @click="chkModifyAlert">
                                        <div class="addAudioFile__icon">
                                            <img src="/assets/images/icon/note1.png" alt="">
                                        </div>
                                        <div class="addAudioFile__info">
                                            <FileUpload name="unTaggedFile" id="unTaggedFile" ref="unTaggedFile"
                                                        target="/beatsomeoneApi/upload_item_file" action="POST" hidden :disabled="!!cit_id"
                                                        @progress="unTaggedFileProgressUpload"
                                                        @start="unTaggedFileStartUpload"
                                                        @finish="unTaggedFileFinishUpload"/>
                                            <p class="form-title required">{{ $t('unTaggedWavOrMp3') }}</p>
                                            <span class="format">{{ !!item.unTaggedFileName ? item.unTaggedFileName : '.WAV (or.MP3)' }}</span>
                                            <div class="addAudioFile__progress">
                                                <span ref="unTaggedFileProgressBar"></span>
                                            </div>
                                        </div>
                                    </label>
                                    <label for="stemFile" class="addAudioFile waves-effect" @click="chkModifyAlert" style="margin-top: 10px;">
                                        <div class="addAudioFile__icon">
                                            <img src="/assets/images/icon/note2.png" alt="">
                                        </div>
                                        <div class="addAudioFile__info">
                                            <FileUpload name="stemFile" id="stemFile" ref="stemFile"
                                                        target="/beatsomeoneApi/upload_item_file" action="POST" hidden :disabled="!!cit_id"
                                                        @progress="stemFileProgressUpload"
                                                        @start="stemFileStartUpload"
                                                        @finish="stemFileFinishUpload"/>
                                            <p>{{ $t('stemsZIPorRAR') }}</p>
                                            <span class="format">{{ !!item.stemFileName ? item.stemFileName : '.ZIP (or.RAR)' }}</span>
                                            <div class="addAudioFile__progress">
                                                <span ref="stemFileProgressBar"></span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="form-item">
                                    <p class="form-title">{{ $t('audiofilesForStreaming') }}</p>
                                    <label for="streamingFile" class="addAudioFile waves-effect" @click="chkModifyAlert">
                                        <div class="addAudioFile__icon">
                                            <img src="/assets/images/icon/note3.png" alt="">
                                        </div>
                                        <div class="addAudioFile__info">
                                            <FileUpload name="streamingFile" id="streamingFile" ref="streamingFile"
                                                        target="/beatsomeoneApi/upload_item_file" action="POST" hidden :disabled="!!cit_id"
                                                        @progress="streamingFileProgressUpload"
                                                        @start="streamingFileStartUpload"
                                                        @finish="streamingFileFinishUpload"/>
                                            <p>{{ $t('customTaggedAudioWAVorMP3') }}</p>
                                            <span class="format">{{ !!item.streamingFileName ? item.streamingFileName : '.WAV (or.MP3)' }}</span>
                                            <div class="addAudioFile__progress">
                                                <span ref="streamingFileProgressBar"></span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-item">
                                <p class="form-title">{{ $t('trackImage') }}</p>
                                <div class="artwork">
                                    <FileUpload name="artworkFile" id="artworkFile" target="/beatsomeoneApi/upload_artwork_file" action="POST" hidden @start="artworkStartUpload" @finish="artworkFinishUpload"/>
                                    <label for="artworkFile" class="artwork-box">
                                        <img :src="!!item.artworkPath ? '/uploads/cmallitem/' + item.artworkPath : '/assets/images/artwork.png'" alt="" id="artworkImg" ref="artworkImg">
                                    </label>
                                    <div class="artwork__info">
                                        {{ $t('preferredImageSize') }}<br/><br/>
                                        {{ $t('makeImageMsg') }}<br/>
                                        <a href="/uploads/data/guide_500x500.zip" target="_blank" download>{{ $t('downloadPSDTemplate') }}</a>
                                    </div>
                                    <label for="artworkFile" class="artwork-upload waves-effect">{{ $t('uploadTrackImage') }}</label>
                                </div>
                            </div>
                        </div>
<!--                        <div class="row">-->
<!--                            <div class="form-item">-->
<!--                                <p class="form-title">{{ $t('urlOfYourTrack') }}</p>-->
<!--                                <div class="input">-->
<!--                                    <input type="text" placeholder="" readonly v-model="item.url"/>-->
<!--                                    <button class="form-copy" type="button">{{ $t('copy') }}</button>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
                    </div>
                </div>
            </section>
            <section class="registered__section">
                <div class="wrap">
                    <h2 class="registered__section-title">{{ $t('sellingPreferences') }}</h2>
                    <div class="registered__section-content">
                        <div class="row row--notice">
                            <div class="registered__notice">
                                <h2>{{ $t('notice') }}</h2> {{ $t('exchangeRateWillBeReflectedAutomatically') }}.
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-item">
                                    <div class="form-title">
                                        <label for="c1" class="checkbox">
                                            <input type="checkbox" hidden="" id="c1" v-model="item.licenseLeaseUseYn">
                                            <span></span> {{ $t('basicLeaseLicensePrice') }}
                                        </label>
                                    </div>
                                    <div class="row row--inner">
                                        <span class="col">
                                            <div class="input">
                                                <input type="number" placeholder="KRW 5500" v-model.number="item.licenseLeasePriceKRW" ref="licenseLeasePriceKRW" @input="onlyNumber($event, 'licenseLeasePriceKRW')"/>
                                            </div>
                                        </span>
                                        <span class="col">
                                            <div class="input">
                                                <input type="number" placeholder="USD 5.00" v-model.number="item.licenseLeasePriceUSD" ref="licenseLeasePriceUSD" @input="onlyNumber($event, 'licenseLeasePriceUSD')"/>
                                            </div>
                                        </span>
                                    </div>
                                    <div class="row row--inner">
                                        <span class="col">
                                            <p>{{ $t('inventoryQuantity') }}</p>
                                        </span>
                                        <span class="col">
                                            <div class="input">
                                                <input type="number" placeholder="0" v-model.number="item.licenseLeaseQuantity" ref="licenseLeaseQuantity" @input="onlyNumber($event, 'licenseLeaseQuantity')"/>
                                            </div>
                                        </span>
                                    </div>
                                    <p class="form-info mt-15">
                                        · {{ $t('itemRegMsg1') }}<br/><br/>
                                        · {{ $t('itemRegMsg2') }} /<br/>
                                        {{ $t('itemRegMsg3') }} /<br/>
                                        {{ $t('itemRegMsg4') }}
                                    </p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-item">
                                    <div class="form-title">
                                        <label for="c2" class="checkbox">
                                            <input type="checkbox" hidden="" id="c2" v-model="item.licenseStemUseYn">
                                            <span></span> {{ $t('masteringLicensePrice') }}
                                        </label>
                                    </div>
                                    <div class="row row--inner">
                                        <span class="col">
                                            <div class="input">
                                                <input type="number" placeholder="KRW 330000" v-model.number="item.licenseStemPriceKRW" ref="licenseStemPriceKRW" @input="onlyNumber($event, 'licenseStemPriceKRW')"/>
                                            </div>
                                        </span>
                                        <span class="col">
                                            <div class="input">
                                                <input type="number" placeholder="USD 280.00" v-model.number="item.licenseStemPriceUSD" ref="licenseStemPriceUSD" @input="onlyNumber($event, 'licenseStemPriceUSD')"/>
                                            </div>
                                        </span>
                                    </div>
                                    <div class="row row--inner">
                                        <span class="col">
                                            <p>
                                                {{ $t('availableQuantityForSale') }}
                                            </p>
                                        </span>
                                        <span class="col">
                                            <div class="input">
                                                <input type="number" placeholder="1" readonly class="disabled" v-model.number="item.licenseStemQuantity" ref="licenseStemQuantity" @input="onlyNumber($event, 'licenseStemQuantity')"/>
                                            </div>
                                        </span>
                                    </div>
                                    <p class="form-info mt-15">
                                        · {{ $t('itemRegMsg5') }}<br/><br/>
                                        · {{ $t('itemRegMsg6') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="registered__section">
                <div class="wrap">
                    <h2 class="registered__section-title">{{ $t('trackDetails') }}</h2>
                    <div class="registered__section-content">
                        <div class="row">
                            <div class="col">
                                <label class="form-item">
                                    <p class="form-title required">{{ $t('primaryGenre') }}</p>
                                    <select v-model="item.genre" class="custom-select-basic">
                                        <option value="">{{ $t('select') }}</option>
                                        <option v-for="(item, index) in listGenre" :key="'genre' + index" :value="item">{{ listGenreName[index] }}</option>
                                    </select>
                                </label>
                                <label class="form-item">
                                    <p class="form-title ">{{ $t('subGenre') }}</p>
                                    <select v-model="item.subgenre" class="custom-select-basic">
                                        <option value="">{{ $t('select') }}</option>
                                        <option v-for="(item, index) in listGenre" :key="'subgenre' + index" :value="item">{{ listGenreName[index] }}</option>
                                    </select>
                                </label>
                                <label class="form-item">
                                    <p class="form-title required">{{ $t('primaryMood') }}</p>
                                    <select v-model="item.moods" class="custom-select-basic">
                                        <option value="">{{ $t('select') }}</option>
                                        <option v-for="(item, index) in listMoods" :key="'moods' + index" :value="item">{{ listMoodsName[index] }}</option>
                                    </select>
                                </label>
                            </div>
                            <div class="col">
                                <label class="form-item">
                                    <p class="form-title ">{{ $t('description') }}</p>
                                    <div class="input">
                                        <textarea v-model.trim="item.cit_content" :placeholder="$t('enterDescription')"></textarea>
                                    </div>
                                </label>
                                <label class="form-item">
                                    <p class="form-title ">{{ $t('bpmDesc') }}</p>
                                    <div class="input">
                                        <input type="number" v-model.trim="item.bpm" ref="bpm" @input="onlyNumber($event, 'bpm')"/>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <div class="registered__btnbox">
<!--                <a href="/beatsomeone/sublist" class="btn btn&#45;&#45;list waves-effect">{{ $t('list') }}</a>-->
                <button type="submit" class="btn btn--save waves-effect" @click="doSubmit" ref="doSubmit">{{ $t('save') }}</button>
            </div>
        </div>
        <Footer/>
    </div>
</template>

<script>
    require('@/assets/js/function')
    import Header from "../include/Header"
    import Footer from "../include/Footer"
    import axios from 'axios'
    import FileUpload from 'vue-simple-upload/dist/FileUpload'
    import { Datetime } from 'vue-datetime'
    import ('vue-datetime/dist/vue-datetime.css')

    export default {
        components: {
            Header, Footer, FileUpload, Datetime
        },
        data: function () {
            return {
                isLogin: false,
                cit_id: null,
                cit_key: null,
                item: {
                    cit_name: '',
                    hashTag: '',
                    trackType: '',
                    cit_start_datetime: null,
                    url: '',
                    licenseLeaseUseYn: false,
                    licenseLeasePriceKRW: '',
                    licenseLeasePriceUSD: '',
                    licenseLeaseQuantity: '',
                    licenseStemUseYn: false,
                    licenseStemPriceKRW: '',
                    licenseStemPriceUSD: '',
                    licenseStemQuantity: 1,
                    genre: '',
                    subgenre: '',
                    moods: '',
                    bpm: '',
                    cit_content: '',
                    unTaggedFile: '',
                    stemFile: '',
                    streamingFile: '',
                    artwork: '',
                    unTaggedFileName: '',
                    stemFileName: '',
                    streamingFileName: '',
                    artworkPath: ''
                },
                uploadInProgress: {
                    unTaggedFile: false,
                    stemFile: false,
                    streamingFile: false,
                    artwork: false
                },
                listGenre: window.genre,
                listMoods: window.moods,
                listTrackType: window.trackType,
                releaseDate: false,
                processStatus: false,
                regLimit: 10
            };
        },
        watch: {
            cit_id: function (n) {
                if (n) {
                    this.getItem()
                } else {
                    this.getItemRegCount()
                }
            },
            cit_key: function (n) {
                if (n) {
                    this.item.url = 'http://beatsomeone.com/beatsomeone/detail/' + n
                }
            }
        },
        computed: {
            minReleaseDate() {
                let date = new Date()
                date.setDate(date.getDate() + 2)
                return date.toISOString()
            },
            listGenreName() {
                let list = [],
                    _self = this

                this.listGenre.forEach(function (val) {
                    list.push(_self.$t('genre' + val.replace(/ /g,"").replace(/-/g,"").replace(/&/g,"")))
                })

                return list
            },
            listMoodsName() {
                let list = [],
                    _self = this

                this.listMoods.forEach(function (val) {
                    list.push(_self.$t('moods' + val.replace(/ /g,"").replace(/-/g,"").replace(/&/g,"")))
                })

                return list
            },
            listTrackTypeName() {
                let list = [],
                    _self = this

                this.listTrackType.forEach(function (val) {
                    list.push(_self.$t('trackType' + val.replace(/ /g,"").replace(/-/g,"").replace(/&/g,"")))
                })

                return list
            }
        },
        methods: {
            chkModifyAlert() {
                if (this.cit_id) {
                    alert(this.$t('notPossibleModifyMusicFileMsg'))
                }
            },
            onlyNumber(event, key) {
                this.$refs[key].value = this.item[key]
            },
            unTaggedFileStartUpload(e) {
                this.startUpload('unTaggedFile', e)
            },
            unTaggedFileFinishUpload(e) {
                this.finishUpload('unTaggedFile', e)
            },
            unTaggedFileProgressUpload(e) {
                this.progressUpload('unTaggedFile', e)
            },
            stemFileStartUpload(e) {
                this.startUpload('stemFile', e)
            },
            stemFileFinishUpload(e) {
                this.finishUpload('stemFile', e)
            },
            stemFileProgressUpload(e) {
                this.progressUpload('stemFile', e)
            },
            streamingFileStartUpload(e) {
                this.startUpload('streamingFile', e)
            },
            streamingFileFinishUpload(e) {
                this.finishUpload('streamingFile', e)
            },
            streamingFileProgressUpload(e) {
                this.progressUpload('streamingFile', e)
            },
            startUpload(type, e) {
                this.uploadInProgress[type] = true
                this.$refs[type + 'ProgressBar'].style.width = '0%'
            },
            finishUpload(type, e) {
                let data = JSON.parse(e.target.response)
                this.uploadInProgress[type] = false

                if (!!data.code && data.code === 'error') {
                    alert('파일 업로드 실패\n파일 확인 후 다시 시도해 주세요')
                    return false
                }

                this.item[type] = JSON.parse(e.target.response)
                this.$refs[type + 'ProgressBar'].style.width = '100%'
            },
            progressUpload(type, e) {
                if (!e) {
                    alert('파일 업로드중 오류가 발생하였습니다. 다시 시도해 주세요.')
                    return false
                }
                this.$refs[type + 'ProgressBar'].style.width = e + '%'
            },
            artworkStartUpload(e) {
                this.uploadInProgress['artwork'] = true
            },
            artworkFinishUpload(e) {
                let data = JSON.parse(e.target.response)
                this.uploadInProgress['artwork'] = false

                if (!!data.code && data.code === 'error') {
                    alert('파일 업로드 실패\n파일 확인 후 다시 시도해 주세요')
                    return false
                }

                this.item.artwork = data
                this.$refs['artworkImg'].src = '/uploads/cmallitem/' + this.item.artwork.filename
            },
            chkHashTag() {
                let hashTag = this.item.hashTag.split(',')
                if (hashTag.length <= 10) {
                    this.$refs['hashTagCount'].innerText = hashTag.length
                    return false
                }

                alert('태그는 최대 10개까지 등록 가능합니다')
                this.item.hashTag = hashTag.slice(0, 10)
                this.$refs['hashTagCount'].innerText = 10
            },
            // 데이터 로딩
            getItem() {
                Http.get(`/beatsomeoneApi/get_item/${this.cit_id}`).then(r => {
                    // 전처리
                    r.data.cde_id_1 = r.data.cde_id || 0
                    r.data.cde_id_2 = r.data.cde_id_2 || 0
                    r.data.cde_id_3 = r.data.cde_id_3 || 0
                    r.data.url = 'http://beatsomeone.com/beatsomeone/detail/' + r.data.cit_key
                    r.data.licenseLeaseUseYn =  r.data.cit_lease_license_use == 1 ? true : false
                    r.data.licenseLeasePriceKRW = r.data.cde_price
                    r.data.licenseLeasePriceUSD = r.data.cde_price_d
                    r.data.licenseLeaseQuantity = r.data.cde_quantity
                    r.data.licenseStemUseYn =  r.data.cit_mastering_license_use == 1 ? true : false
                    r.data.licenseStemPriceKRW = r.data.cde_price_2
                    r.data.licenseStemPriceUSD = r.data.cde_price_d_2
                    r.data.licenseStemQuantity = 1
                    r.data.unTaggedFileName = r.data.cde_originname
                    r.data.stemFileName = r.data.cde_originname_2
                    r.data.streamingFileName = r.data.cde_originname_3
                    r.data.artworkPath = r.data.cit_file_1
                    r.data.artwork = ''
                    r.data.cit_start_datetime = !r.data.cit_start_datetime ? '' : new Date(Date.parse(r.data.cit_start_datetime)).toISOString()
                    this.item = r.data
                })
            },
            getItemRegCount() {
                // Http.get('/beatsomeoneApi/item_reg_count').then(r => {
                //     if (r.data.count > this.regLimit) {
                //         alert(this.$t('registrationLimitExceededMsg'))
                //         window.location.href = '/'
                //     }
                // });
            },
            // 저장
            doSubmit() {
                if (this.processStatus) {
                    return false
                }

                const f = new FormData()

                if (!this.item.cit_name) {
                    alert(this.$t('enterSubject'))
                    return false
                }
                if (!this.item.trackType) {
                    alert(this.$t('selectTrackType'))
                    return false
                }
                if (!this.item.cit_start_datetime) {
                    alert(this.$t('selectReleaseDate'))
                    return false
                }

                if (!this.item.unTaggedFile && !this.item.unTaggedFileName) {
                    alert(this.$t('registerSoundSource'))
                    return false
                }

                if (this.item.licenseStemUseYn && !this.item.stemFile && !this.item.stemFileName) {
                    alert(this.$t('attachStemsFile'))
                    return false
                }

                if (!this.item.licenseLeaseUseYn && !this.item.licenseStemUseYn) {
                    alert(this.$t('selectSalesType'))
                    return false
                }

                if (this.item.licenseLeaseUseYn) {
                    if (!this.item.licenseLeasePriceKRW) {
                        alert(this.$t('enterRentalPrice') + ' (KRW)')
                        return false
                    }

                    if (!this.item.licenseLeasePriceUSD) {
                        alert(this.$t('enterRentalPrice') + ' (USD)')
                        return false
                    }

                    if (!this.item.licenseLeaseQuantity) {
                        alert(this.$t('enterNumberRentalsAvailable'))
                        return false
                    }
                }

                if (this.item.licenseStemUseYn) {
                    if (!this.item.licenseStemPriceKRW) {
                        alert(this.$t('enterSalesPrice') + ' (KRW)')
                        return false
                    }

                    if (!this.item.licenseStemPriceUSD) {
                        alert(this.$t('enterSalesPrice') + ' (USD)')
                        return false
                    }
                }

                if (!this.item.genre) {
                    alert(this.$t('selectType'))
                    return false
                }
                if (!this.item.moods) {
                    alert(this.$t('chooseMood'))
                    return false
                }

                for (let key in this.uploadInProgress) {
                    if (this.uploadInProgress[key]) {
                        alert(this.$t('uploadingFiles'))
                        return false
                    }
                }

                let param
                _.forEach(this.item, (v, k) => {
                    param = (typeof v !== "object") ? v : JSON.stringify(v)
                    f.append(k, param)
                });

                if (this.cit_id) {
                    f.append('cit_id', this.cit_id)
                }

                if (this.cit_key) {
                    f.append('cit_key', this.cit_key)
                }

                this.processStatus = true
                this.$refs.doSubmit.disabled = true
                this.$refs.doSubmit.innerHTML = 'Processing...'

                axios.post('/beatsomeoneApi/merge_item', f, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(r => {
                    log.debug(
                        {
                            'MERGE SUCCESS': r.data,
                        }
                    )
                    if (!this.cit_id) {
                        if (confirm(this.$t('registerAdditionalMusic'))) {
                            window.location.href = '/mypage/regist_item/'
                        } else {
                            window.location.href = '/'
                        }
                    } else {
                        alert(this.$t('itIsChanged'))
                        window.location.href = '/mypage/regist_item/' + r.data
                    }
                }, e => {
                    alert(this.cit_id ? this.$t('modificationFailedMsg') : this.$t('registrationFailedMsg'))
                    log.debug('ERROR', e)
                })
            },
        }
    }
</script>

<style lang="scss">
    @import '@/assets/scss/App.scss';
</style>

<style scoped="scoped" lang="css">
    @import '/assets/plugins/slick/slick.css';
    @import '/assets/plugins/rangeSlider/css/ion.rangeSlider.min.css';
</style>
