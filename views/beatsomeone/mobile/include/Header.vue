<template>

    <div>
        <header class="header">
            <div class="wrap">
                <h1 class="header__logo">
                    <a href="/"><img src="@/assets_m/images/logo.png" alt=""/></a>
                </h1>
                <div class="header__btnbox">
                    <a href="javascript:;" class="header__locale" v-if="!isShowSearchBox" @click="toggleLocale()">{{ toggleLocaleMenuTit }}</a>
                    <input type="text"

                           v-if="isShowSearchBox"
                           v-model="searchText"
                           @keyup.enter="enterClicked()"
                           class="mo-header-input"
                           style="width: 175px;
                                position:absolute;
                                top: 8px;
                                right:60px;
                                height: 25px;
                                color: #fff;
                                background: rgba(255, 255, 255, 0.2);
                                border-radius: 2em;
                                font-size: 12px;
                                padding: 0 35px 0 10px;
                                -webkit-transition: all 0.3s;
                                transition: all 0.3s;"/>
                    <a href="javascript:;" class="header__search" @click="clickSearchButton" style="z-index: 10000"></a>
                    <a class="header__nav" @click="toggleOpenMenu"></a>
                </div>
            </div>
        </header>

        <div class="gnb" v-if="isOpen">
            <div class="gnb__bg" ></div>
        </div>

<!--        <transition name="slide-fade">-->
        <nav class="gnb" v-if="isOpen" >

            <div class="gnb__content">
                <a class="gnb__close" @click="toggleOpenMenu">닫기</a>
                <div class="gnb__links">
                    <a href="/mypage/favorites">{{ $t('favorite') }}</a>
                    <a v-if="isCustomer" href="">{{ $t('freeBeats') }}</a>
                    <a v-if="isSeller" href="/mypage/regist_item">{{ $t('registrationSources') }}</a>
                    <a href="/mypage" v-if="isLogin">{{ $t('mypage') }}</a>
                    <a href="/login/logout?/" v-if="isLogin">{{ $t('logout') }}</a>
                    <a href="/login" v-if="!isLogin">{{ $t('login') }}</a>
                    <a href="/register" v-if="!isLogin">{{ $t('signup') }}</a>
                    <a href="/register" class="sale_signup" v-if="!isLogin">{{ $t('saleSignup') }}</a>
                    <a href="/cmall/cart" class="header__cart" v-if="isLogin">
                        {{ $t('cart')}}<br>
                        ({{ $t('currencySymbol') }}{{ $i18n.locale == 'en' ? getCartSumD : getCartSum }})
                    </a>
                </div>
                <div v-html="banner_content" class="gnb__banner">

                </div>

<!--                <a href="" class="gnb__banner">-->
<!--                    <img src="@/assets_m/images/gnb-banner.png" alt="">-->
<!--                </a>-->
            </div>

        </nav>
<!--        </transition>-->
    </div>


</template>

<script>

    import { EventBus } from '*/src/eventbus';
    import Vuecookies from 'vue-cookies';
    import axios from 'axios';

    export default {
        name: 'Header',
        data: function () {
            return {
                userInfo: false,
                searchText: null,
                cartSum: 0,
                isOpen: false,
                isShowSearchBox: false,
                member_group_name: '',
                banner_content: ''
            };
        },
        created() {
            // this.fetchUserInfo();
            this.userInfo = window.member;
            this.member_group_name = window.member_group_name;
            EventBus.$on('add_cart',() => {
                this.updateCartSum();
            });
        },
        mounted() {
            this.updateCartSum();

            axios.get('/beatsomeoneApi/get_banner')
                .then(res => res.data)
                .then(data=> {
                    this.banner_content = data.content;
                })
                .catch(error => {
                    console.error(error);
                })
        },
        computed: {
            toggleLocaleMenuTit: function() {
                return this.$i18n.locale === 'en' ? 'KOR' : 'ENG';
            },
            isSeller() {
                return this.member_group_name.includes('seller')
            },
            isCustomer() {
                return this.member_group_name === 'buyer';
            },
            isLogin () {
                return this.userInfo !== false;
            },
            getCartSum() {
                return Number(this.$store.getters.getCartSum).toLocaleString('ko-KR', {minimumFractionDigits: 0});
            },
            getCartSumD() {
                return Number(this.$store.getters.getCartSumD).toLocaleString(undefined, {minimumFractionDigits: 2});
            },
        },
        methods: {
            fetchUserInfo() {
              Http.post('/beatsomeoneApi/get_user_info').then(r=> {
                  this.userInfo = r[0];
              });
            },
            toggleOpenMenu() {
              this.isOpen = !this.isOpen;

            },
            updateCartSum() {
                axios.get('/beatsomeoneApi/getCartSum')
                    .then(res => res.data)
                    .then(data => {
                        this.cartSum = data.s;
                        this.cartSumD = data.s_d;
                        this.$store.dispatch('addMoney', {
                            money: data.s,
                            money_d: data.s_d,
                        })
                    })
                    .catch(error => {
                        console.log(error);
                    })
            },
            clickSearchButton() {
                this.isShowSearchBox = !this.isShowSearchBox;
                if(this.isShowSearchBox === false){
                    this.search();
                }
            },
            search() {
                if (!this.searchText) {
                    return;
                }
                // const path = `/beatsomeone/search?q=${this.searchText}`;
                const path = `/beatsomeone/sublist?genre=All%20Genre&search=${this.searchText}`;
                window.location.href = path;
            },
            enterClicked(){
                this.search();
            },
            toggleLocale() {
                let locale = this.$i18n.locale === 'en' ? 'ko' : 'en'
                Vuecookies.set('locale', locale)
                this.$i18n.locale = locale
            },
            moveAction(o) {
                let url = null;
                // 로그인시
                if(this.userInfo) {
                    switch(o) {
                        case 'freeBeats': {
                            url = this.userInfo.mem_usertype == 1 ? '무료비트URL수정필요' : '음원등록URL수정필요';
                            break;
                        }
                    }
                }
                // 비로그인시
                else {
                    url = '무료비트URL수정필요';
                }

                // 이동
                window.location.href = url;
            },
        },
    }

</script>

<style scoped="scoped" type="css">
    .slide-fade-enter-active {
        transition: all .3s ease;
    }
    .slide-fade-leave-active {
        transition: all .5s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }
    .slide-fade-enter, .slide-fade-leave-to
        /* .slide-fade-leave-active below version 2.1.8 */ {
        transform: translateX(100px);
        opacity: 0;
    }

</style>