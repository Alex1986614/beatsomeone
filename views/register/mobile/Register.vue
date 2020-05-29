<template>
    <div class="wrapper">

        <Header :is-login="isLogin"></Header>
<!--        <div style="margin-top: 100px;">-->
<!--            <pre>{{ info }}</pre>-->
<!--&lt;!&ndash;            <a type="button" @click="doJoin">가입</a>&ndash;&gt;-->
<!--        </div>-->


        <router-view />
        <Footer></Footer>
    </div>

</template>

<script>

    require('@/assets_m/js/function');

    import Header from "@/views/beatsomeone/mobile/include/Header";
    import Footer from "@/views/beatsomeone/mobile/include/Footer";
    import { EventBus } from '*/src/eventbus';

    export default {
        components: {Header,Footer},
        data: function() {
            return {
                info: {},
                isLogin: false,
            }
        },
        created() {
            let tmp = { userType: 'musician', plan: 'free', planName: 'free', billTerm: 'monthly'  }
            Object.assign(this.info,tmp)

            EventBus.$on('submit_join_form', d => {
               Object.assign(this.info,d);
            });
            EventBus.$on('finish_join_form', d => {
                Object.assign(this.info,d);
                this.doJoin();
            });

            // this.info = {
            //     "userType": "user",
            //     "accountType": "email",
            //     "username": "222",
            //     "email": "333",
            //     "password": "444",
            //     "type": "Recording Artist"
            // };

            // this.info = {
            //     "userType": "musician",
            //     "plan": "pro",
            //     //"plan": "free",
            //     //"billTerm": "monthly",
            //     "billTerm": "yearly",
            //     "accountType": "email",
            //     "username": "1111",
            //     "email": "11@11.11",
            //     "password": "1111",
            //     "type": "Music Lover",
            //     "firstname": "111",
            //     "lastname": "222",
            //     "location": "222",
            //     "introduce": "222"
            // };
        },
        mounted() {
            // 중간 리프레시 초기화
            // if(this.$router.currentRoute.path != '/') {
            //     this.$router.push({path: '/'});
            // }
        },
        watch: {

        },
        methods: {
            doJoin() {
                const form = {
                    mem_type: this.info.type,
                    user_type: this.info.userType,
                    mem_userid : this.info.username,
                    mem_nickname : this.info.username,
                    mem_password : this.info.password,
                    mem_username : this.info.firstname + ' ' + this.info.lastname,
                    mem_firstname : this.info.firstname,
                    mem_lastname : this.info.lastname,
                    mem_email : this.info.email,
                    mem_address1 : this.info.location,
                    mem_profile_content : this.info.introduce,
                    mem_musician_bank : this.info.mem_musician_bank,
                    mem_musician_account_nm : this.info.mem_musician_account_nm,
                    mem_musician_account : this.info.mem_musician_account,
                    promo_code : this.info.promo_code
                };

                Http.post('/register/ajax_form_user',form).then(r => {
                    // alert(this.$t('registerSuccess')) ;
                    //window.location.href = '/';
                    // this.$router.push({path: '/6'});
                    window.location.href = '/mypage/regist_item';
                },e => {
                    alert(this.$t('registerFail'));
                });
            },
        },
    }
</script>

<style lang="scss">
    @import '@/assets_m/scss/App.scss';
</style>

<style lang="css">

</style>
