<template>
    <header class="header">
        <div class="smtm9-top" v-if="false">
            <a href="/smtm9"><img src="/assets/images/event/smtm9/top.jpg"></a>
        </div>
        <div class="wrap">
            <h1 class="header__logo">
                <a href="/"><img src="/assets/images/logo.png" alt=""/></a>
            </h1>
            <div class="header__gnb">
                <div class="header__search">
                    <div>
                        <input type="text" v-model="searchText" @keyup.enter="search"/>
                        <button @click="search"></button>
                    </div>
                </div>
                <nav class="header__nav">
                    <a href=""></a>
                    <a href="/mypage/favorites">{{ $t('favorite') }}</a>
                    <a v-if="isCustomer" href="">{{ $t('freeBeats') }}</a>
                    <a v-if="isSeller" href="/mypage/regist_item">{{ $t('registrationSources') }}</a>
                    <a href="/mypage" v-if="isLogin">{{ $t('mypage') }}</a>
                    <a href="/login/logout" v-if="isLogin">{{ $t('logout') }}</a>
                    <a href="/login" v-if="!isLogin">{{ $t('login') }}</a>
                    <a href="/register" v-if="!isLogin" @click="UserOffer('buyer')">{{ $t('signup') }}</a>
                    <a href="/register" class="sale_signup" v-if="!isLogin" @click="UserOffer('seller')" data-toggle="tooltip" data-placement="bottom" title="Hooray!">{{ $t('saleSignup') }}</a>
                    <a href="/cmall/cart" class="header__cart" v-if="isLogin">({{ $t('currencySymbol') }}{{ $i18n.locale == 'en' ? getCartSumD : getCartSum }})</a>
                    <a href="javascript:;" @click="toggleLocale()">{{ toggleLocaleMenuTit }}</a>
                </nav>
            </div>
        </div>
    </header>
</template>

<script>
    import { EventBus } from '*/src/eventbus';
    import Vuecookies from 'vue-cookies';
    import axios from 'axios';
    import { mapGetters, mapActions } from 'vuex';

    export default {
        name: 'Header',
        data: function () {
            return {
                searchText: '',
                cartSum: 0,
                member_group_name: '',
                member: false
            };
        },
        created() {
            EventBus.$on('add_cart',() => {
                this.updateCartSum();
            });
        },
        mounted() {
            this.updateCartSum();
            this.member_group_name = window.member_group_name;
            this.member = window.member;
        },
        computed: {
            toggleLocaleMenuTit: function() {
                return this.$i18n.locale === 'en' ? 'KOR' : 'ENG';
            },
            getCartSum() {
                return Number(this.$store.getters.getCartSum).toLocaleString('ko-KR', {minimumFractionDigits: 0});
            },
            getCartSumD() {
                return Number(this.$store.getters.getCartSumD).toLocaleString(undefined, {minimumFractionDigits: 2});
            },
            isSeller() {
                return this.member_group_name.includes('seller')
            },
            isCustomer() {
                return this.member_group_name === 'buyer';
            },
            isLogin () {
                return this.member !== false;
            },
           
        },
        methods: {
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
            search() {
                // if(!this.searchText) {
                //     return;
                // }
                const path = `/beatsomeone/sublist?genre=All Genre&search=${this.searchText ?? ''}`;
                window.location.href = path;
            },
            toggleLocale() {
                let locale = this.$i18n.locale === 'en' ? 'ko' : 'en'
                Vuecookies.set('locale', locale)
                this.$i18n.locale = locale
            },
            UserOffer(state) {
                console.log('this is user_______', state)
                this.$store.commit('SET_USER_Offer', state);
            }
        },
    }

</script>

<style scoped="scoped">

    .header .header__nav a {
        cursor: pointer !important;
    }

    .smtm9-top {
        text-align:center;
        background-color: #000000;
        width: 100%;
    }

    .smtm9-top img {
        max-width: 100%;
    }

</style>