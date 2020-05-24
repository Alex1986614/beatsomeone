<template>
    <div class="wrapper">
        <Header :is-login="isLogin"></Header>
        <div class="container accounts">
            <div class="accounts__title">
                <h1>
                    {{ $t('loginToContinue') }}
                </h1>
            </div>
            <div class="login accounts__defaultLayout">
                <form action="/login" method="post">
                    <div class="accounts__form">
                        <div class="row">
                            <label for="">
                                <p class="form-title">{{ $t('emailOrUsername') }}</p>
                                <div class="input">
                                    <input ref="userid"
                                            type="text" name="mem_userid"
                                           :placeholder="$t('typeEmailOrUsername')"
                                    />
                                </div>
                            </label>
                        </div>
                        <div class="row">
                            <label for="">
                                <p class="form-title">{{ $t('password') }}</p>
                                <div class="input">
                                    <input type="password" name="mem_password" :placeholder="$t('typeYourPassword')" />
                                </div>
                            </label>
                        </div>
                    </div>
                    <div v-html="errorMsg" v-if="errorMsg" >

                    </div>
                    <div class="accounts__btnbox">
                        <button type="submit" class="btn btn--submit">
                            {{ $t('login') }}
                        </button>
                    </div>
                </form>

<!--                <div class="accounts__social">-->
<!--                    <h2>{{ $t('snsLogin') }}</h2>-->
<!--                    <a href=""-->
<!--                    ><img-->
<!--                            src="@/assets/images/accounts-facebook.png"-->
<!--                            :alt="$t('loginFacebook')"-->
<!--                    /></a>-->
<!--                    <a href=""-->
<!--                    ><img-->
<!--                            src="@/assets/images/accounts-twitter.png"-->
<!--                            :alt="$t('loginTwitter')"-->
<!--                    /></a>-->
<!--                    <a href=""-->
<!--                    ><img src="@/assets/images/accounts-google.png" :alt="$t('loginGoogle')"-->
<!--                    /></a>-->
<!--                    <a href=""-->
<!--                    ><img src="@/assets/images/accounts-naver.png" :alt="$t('loginNaver')"-->
<!--                    /></a>-->
<!--                    <a href=""-->
<!--                    ><img src="@/assets/images/accounts-kakao.png" :alt="$t('loginKakao')"-->
<!--                    /></a>-->
<!--                </div>-->

                <div class="accounts__etc">
                    {{ $t('dontHaveAccount') }} <a href="/register#/3">{{ $t('signup') }}</a>
                </div>
<!--                <div class="accounts__etc">-->
<!--                    {{ $t('forgotYourAccount') }} <a href="/findaccount">{{ $t('findAccount') }}</a>-->
<!--                </div>-->
            </div>
        </div>
        <Footer></Footer>
    </div>
</template>

<script>
    require('@/assets/js/function');

    import Header from "@/views/beatsomeone/basic/include/Header";
    import Footer from "@/views/beatsomeone/basic/include/Footer";

    import { EventBus } from '*/src/eventbus';

    export default {
        components: {Header,Footer},
        data: function() {
            return {
                errorMsg: '',
                isLogin: false,
            }
        },
        mounted() {
            this.$refs.userid.focus();
        },
        watch: {
        },
        methods: {
        },
    }
</script>

<style lang="scss">
    @import '@/assets/scss/App.scss';
</style>

<style lang="css">
    @import '/assets/plugins/slick/slick.css';
    @import '/assets/plugins/rangeSlider/css/ion.rangeSlider.min.css';
</style>
