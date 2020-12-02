<template>
    <div class="container accounts">
        <div class="accounts__title">
            <h1>
                {{ $t('signup') }}
            </h1>
            <p>
                {{ $t('createMsg') }}
            </p>
        </div>
        <div class="login accounts__defaultLayout">
            <div class="accounts__btnbox border-none">
                <button type="submit" class="btn btn--white" @click="doNext('email')">
                    <img src="@/assets/images/icon/email.png" alt="" />
                    {{ $t('signupWithEmail') }}
                </button>
            </div>

            <div class="accounts__social">
                <h2>{{ $t('snsLogin') }}</h2>
                <a href="#" @click="social_connect_on('facebook')"><img
                        src="@/assets/images/accounts-facebook.png"
                        :alt="$t('loginFacebook')"
                /></a>
                <a href="#"  @click="social_connect_on('twitter')"
                ><img
                        src="@/assets/images/accounts-twitter.png"
                        :alt="$t('loginTwitter')"
                /></a>
                <a href="#"  @click="social_connect_on('google')"
                ><img src="@/assets/images/accounts-google.png" :alt="$t('loginGoogle')"
                /></a>
                <a href="#"  @click="social_connect_on('naver');"
                ><img src="@/assets/images/accounts-naver.png" :alt="$t('loginNaver')"
                /></a>
                <a href="#"  @click="social_connect_on('kakao')"
                ><img src="@/assets/images/accounts-kakao.png" :alt="$t('loginKakao')"
                /></a>
            </div>

            <div class="accounts__etc">
                {{ $t('alreadyAccount') }} <a href="/login">{{ $t('login') }}</a>
            </div>
        </div>
    </div>
</template>

<script>

    import { EventBus } from '*/src/eventbus';

    export default {

        data: function() {
            return {

            }
        },
        created() {

        },
        mounted() {


        },
        watch: {

        },
        methods: {
            doNext(type) {
                if (type === 'email') {
                    this.$router.push('/3');
                }
            },
            social_connect_on(social_type) {
                if (social_type !== 'facebook' && social_type !== 'twitter' && social_type !== 'google' && social_type !== 'naver' && social_type !== 'kakao') {
                    return false;
                }
                let social_popup = window.open('/social/' + social_type + '_login', social_type + '-on', 'width=600,height=600');
                social_popup.onbeforeunload = this.socialConnected
                console.log('OK2');
            },
            socialConnected() {
                console.log('OK3');
                let userInfo = this.$store.getters.getUserInfo;
                window.location.href = `/register/purchase?mgr_id=${userInfo.group.mgr_id}&billTerm=${userInfo.billTerm}`;
            }
        },

    }




</script>

<style lang="scss">


</style>

<style lang="css">

</style>
