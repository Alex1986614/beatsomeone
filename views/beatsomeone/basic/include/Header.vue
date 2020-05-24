<template>

    <header class="header">
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
                    <a href="/mypage/regist_item" v-if="isLogin">{{ $t('registrationSources') }}</a>
                    <a href="/login/logout?/" v-if="isLogin">{{ $t('logout') }}</a>
                    <a href="/login" v-if="!isLogin">{{ $t('login') }}</a>
                    <a href="/register" v-if="!isLogin">{{ $t('signup') }}</a>
                    <a href="#" @click="toggleLocale()">{{ toggleLocaleMenuTit }}</a>
                </nav>
            </div>
        </div>
    </header>
</template>

<script>
    import { EventBus } from '*/src/eventbus';
    import Vuecookies from 'vue-cookies'

    export default {
        name: 'Header',
        props: {
            isLogin : {
                type: Boolean,
                default: false,
            }
        },
        data: function () {
            return {
                searchText: null,
                cartSum: 0,
            };
        },
        watch: {
          isLogin: function (n) {

          },
        },
        created() {
            EventBus.$on('add_cart',() => {
                this.updateCartSum();
            });
        },
        mounted() {
            this.updateCartSum();
        },
        computed: {
            toggleLocaleMenuTit: function() {
                return this.$i18n.locale === 'en' ? 'KOR' : 'ENG';
            },
        },
        methods: {
            updateCartSum() {
                Http.post( `/beatsomeoneApi/getCartSum`).then(r=> {
                    if(r >= 0) {
                        this.cartSum = r;
                    }
                });
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
        },
    }

</script>

<style scoped="scoped">

</style>