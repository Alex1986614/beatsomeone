<template>
    <div class="wrapper">
        <Header :is-login="isLogin"></Header>
        <div class="container">
            <div class="main">
                <div class="filter"></div>
                <div class="wrap videolist">
                    <div class="videoview">
                        <div class="title">[비트썸바디] 비트메이커 KKOMA 인터뷰</div>
                        <div class="desc">
                            Beat someone에서 만난 비트메이커 KKOMA!<br/>
                            그의 비트에 대한 철학과 아끼는 비트 BEST 3를 만나보세요.
                        </div>
                    </div>
                    <div class="block">
                        <div>
                            <iframe width="100%" height="auto" src="https://www.youtube.com/embed/e5VmOD1Sn04" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            <!-- <div class="play">
                                <img src="/assets/images/icon/hover_play.png">
                            </div> -->
                        </div>
                    </div>
                    <div class="sns-box">
                        <div class="iconbtnbox">
                            <button><img src="/assets/images/icon/share.png"/></button>
                            <button><img src="/assets/images/icon/fb.png"/></button>
                            <button><img src="/assets/images/icon/tw.png"/></button>
                        </div>
                        <div class="iconbtnbox">
                            <button><img src="/assets/images/icon/list.png"/></button>
                        </div>
                    </div>
                </div>
                <Footer></Footer>
            </div>
        </div>
    </div>
</template>


<script>
    require('@/assets_m/js/function')
    import Header from "../include/Header"
    import Footer from "../include/Footer"

    import $ from "jquery";
    import { EventBus } from '*/src/eventbus';
    import Velocity from "velocity-animate";
    //import MainPlayer from "@/vue/common/MainPlayer";
    import WaveSurfer from 'wavesurfer.js';

    export default {
        components: {
            // Header, Footer
        },
        data: function() {
            return {
                isLogin: false,
                group_title: 'SELLER',
                product_status: 'PENDING',
                popup_filter:0,
                ws: null,
                isPlay: false,
                isReady: false,
                wavesurfer: null,
            };
        },
        mounted(){
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
        },
        created() {
                Http.get('/beatsomeoneApi/get_user_regist_item_list').then(r => {
                    console.log(r.data);
                    this.myProduct_list = r.data;
                });
        },
        methods:{
            goPage: function(page){
                window.location.href = '/mypage/'+page;
            },
            calcSeq: function(size, i){
                return parseInt(size) - parseInt(i);
            },
            formatCitName: function(data){
                var rst;
                var limitLth = 50
                if(limitLth < data.length && data.length <= limitLth*2){
                    rst = data.substring(0,limitLth) + '<br>' + data.substring(limitLth,limitLth*2);
                }else if(limitLth < data.length && limitLth*2 < data.length){
                    rst = data.substring(0,limitLth) + '<br>' + data.substring(limitLth,limitLth*2) + '...';
                }else{
                    rst = data
                }
                return rst;
            },
            productEditBtn: function(key){
                console.log("productEditBtn:" +key);
                window.location.href = 'https://beatsomeone.com/detail/'+key;
            },
            playAudio(i) {
                this.wavesurfer = WaveSurfer.create({
                    container: document.querySelector('#waveform'),
                });
                // https://nachwon.github.io/waveform/
                this.wavesurfer.load('https://beatsomeone.com/uploads/cmallitemdetail/2020/04/cb40bdf9165462c6351ebd82abedb1d6.mp3');
                this.wavesurfer.on('ready', this.start);
            },
            start(){
                this.wavesurfer.play();
            },
        }
    }
</script>


<style lang="scss">
    @import '@/assets_m/scss/App.scss';
</style>

<style scoped="scoped" lang="scss">
    @import '/assets/plugins/slick/slick.css';
    @import '/assets/plugins/rangeSlider/css/ion.rangeSlider.min.css';
    .main .filter {
        height: 64px;
    }
    .videoview {
        padding: 12px 4px;
        .title {
            font-size: 14px;
            font-weight: 600;
            color: white;
            overflow: hidden;
            height: 18px;
            line-height: 18px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 10px;
        }
        .desc {
            font-size: 12px;
            font-weight: normal;
            color: rgba(white,.7);
            line-height: 15px;
            height: 30px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    }
    .sns-box {
        margin-top: 20px;
        text-align: center;
    }
    .iconbtnbox > * {
        padding: 2px;
        width: 32px;
        height: 32px;
    }
</style>