<template>
    <div style="margin-bottom:100px;">
        <div class="row" style="margin-bottom:30px;">
            <div class="title-content">
                <div class="title"> Refund detail </div>
                <div class="payment_box">
                    <div class="n-box">
                        <div class="n-flex">
                            <span class="title">{{$t('orderNumber')}}</span> <span style="margin-top: 0; color: rgba(255,255,255,.7);">{{ order.cor_id }}</span>
                        </div>
                        <div class="n-flex">
                            <span class="title">Date</span>
                            <span style="margin-top: 0; color: rgba(255,255,255,.3)">{{ order.cor_datetime }}</span>
                        </div>
                        <div class="n-flex">
                            <div class="title">Status</div>
                            <div v-if="order.cor_status === '1'" class="green">
                                {{ $t('orderComplete') }}
                            </div>
                            <div v-else-if="order.cor_status === '2'" class="red">
                                {{ $t('orderCancel') }}
                            </div>
                            <div v-else class="yellow">
                                {{ $t('deposit') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="main__media board inquirylist">

            </div>

        </div>

        <div class="row" style="margin-bottom:10px;">

            <div class="title-content">
                <div class="title">
                    <div><span class="yellow">{{ orderItems.length }}</span> Requested items</div>
                </div>
            </div>

            <div class="playList productList orderlist" style="margin-top:10px;">
                <ul>
                    <OrderDetailItem v-for="(item, idx) in orderItems" :cor_id="cor_id" :item="item" v-bind:key="idx" :pg="order.cor_pg" />
                </ul>
            </div>

        </div>

        <div class="row">
            <div class="title-content text-info">
                <p>{{$t('downloadNotAvailableWhenDepositMsg')}}</p>
                <p>{{$t('downloadAvailable60Msg')}}</p>
            </div>
        </div>

        <div class="row">
            <div class="payment_box">
                <div class="n-box">
                    <div class="n-flex between">
                        <span class="title">{{ $t('payMethod1') }}</span>
                        <span style="font-weight: 600;">{{ $t(order.cor_pg) }}</span>
                    </div>
                    <div class="n-flex between">
                        <span class="title">{{ $t('payMethodDetail') }}</span>
                        <span style="font-weight: 600;">{{ $t(paymentMethod) }}</span>
                    </div>
                    <div class="n-flex between">
                        <span class="title">{{ $t('lang72') }}</span>
                        <span class="yellow" style="font-weight: 600;">{{ formatPr(order.cor_pg, order.cor_refund_price) }}</span>
                    </div>
                    <div class="n-flex between" style="padding-top:30px; margin-top:20px; border-top:1px solid rgba(255,255,255,.3);" v-if="order.cor_cancel_datetime">
                        <span class="title">{{ $t('lang73') }}</span>
                        <span style="opacity:.7; font-weight:normal;">{{ order.cor_cancel_datetime }}</span>
                    </div>
                    <div class="n-flex between" v-if="order.cor_refund_datetime">
                        <span class="title">{{ $t('lang74') }}</span>
                        <span style="opacity:.7; font-weight:normal;">{{ order.cor_refund_datetime }}</span>
                    </div>
                    <div class="n-flex between">
                        <span class="title">{{ $t('lang75') }}</span>
                        <span style="opacity:.7; font-weight:normal;">{{ order.cor_memo }}</span>
                    </div>
                    <div class="n-flex between">
                        <span style="opacity:.7; font-weight:600;">{{ order.cor_admin_memo }}</span>
                    </div>
                    <div class="n-flex between" style="padding-top:30px; margin-top:20px; border-top:1px solid rgba(255,255,255,.3);">
                        <span class="title">{{ $t('lang76') }}</span>
                        <span class="red">{{ formatPr(order.cor_pg, order.cor_refund_price) }}</span>
                    </div>
                    <div class="n-flex between">
                        <span class="title">{{ $t('lang77') }}</span>
                        <span class="red">{{ order.cor_refund_point }} P</span>
                    </div>
                </div>
            </div>
            <p class="desc">
                <img data-v-6049000a="" src="/assets/images/icon/info_blue.png">
                <span>{{$t('depositWaitingStateSupportCaseMenuMsg')}} <a href="/mypage#/inquiry/">{{$t('shortcut')}}</a></span>
            </p>
        </div>

        <div class="n-flex n-btnbox">
            <button class="btn btn--gray" @click="goPage('/mycancelList')">{{$t('backToList')}}</button>
        </div>
    </div>
</template>


<script>
    import $ from "jquery";
    import WaveSurfer from 'wavesurfer.js';
    import axios from "axios";
    import OrderDetailItem from "./OrderDetailItem";

    export default {
        components: {
            OrderDetailItem,
        },
        data: function() {
            return {
                isLogin: false,
                item: {},
                order: {},
                orderItems: []
            };
        },
        mounted(){
            this.cor_id = this.$route.params.cor_id;
            axios.get(`/cmall/ajax_orderresult/${this.cor_id}`)
                .then(res => res.data)
                .then(data => {
                    this.order = data.data;
                    if (this.order.cor_refund_point == undefined) this.$set(this.order, 'cor_refund_point', 0);
                    this.orderItems = data.orderdetail;
                    this.orderItems.forEach(item => {
                        this.$set(item, 'is_selected', false);
                    })
                    // this.funcDesc();
                })
                .catch(error => {
                    console.error(error);
                })
        },
        created() {
        },
        methods:{
            goPage(path) {
                this.$router.push(path);
            },
            checkToday: function (date) {
                const input = new Date(date);
                const today = new Date();
                return (
                    input.getDate() === today.getDate() &&
                    input.getMonth() === today.getMonth() &&
                    input.getFullYear() === today.getFullYear()
                );
            },
            truncate(str, n) {
                return (str.length > n) ? str.substr(0, n-1) + '...' : str;
            },
            getGenre(g1, g2) {
                if (this.isEmpty(g2)) {
                    return g1;
                } else {
                    return g1 + ", " + g2;
                }
            },
            formatCitName: function (data, limitLth) {
                var rst;
                if (limitLth < data.length && data.length <= limitLth * 2) {
                    rst =
                        data.substring(0, limitLth) +
                        "<br>" +
                        data.substring(limitLth, limitLth * 2);
                } else if (limitLth < data.length && limitLth * 2 < data.length) {
                    rst =
                        data.substring(0, limitLth) +
                        "<br>" +
                        data.substring(limitLth, limitLth * 2) +
                        "...";
                } else {
                    rst = data;
                }
                return rst;
            },
            isEmpty: function (str) {
                if (typeof str == "undefined" || str == null || str === "") return true;
                else return false;
            },
            formatPrice: function (kr, en, pg) {
                if (pg === "paypal") {
                    return (
                        "$ " +
                        Number(en).toLocaleString(undefined, {minimumFractionDigits: 2})
                    );
                } else {
                    return (
                        "₩ " +
                        Number(kr).toLocaleString("ko-KR", {minimumFractionDigits: 0})
                    );
                }
            },
            formatPr: function (pg, price) {
                if (pg === 'paypal') {
                    return '$' + this.formatNumberEn(price);
                } else if (pg === 'allat') {
                    return '₩' + this.formatNumber(price);
                } else {
                    return price
                }
            },
            formatNumber(n) {
                //Number(n).toLocaleString('en', {minimumFractionDigits: 3});
                return Number(n).toLocaleString(undefined, {minimumFractionDigits: 0});
            },
            formatNumberEn(n) {
                //Number(n).toLocaleString('en', {minimumFractionDigits: 3});
                return Number(n).toLocaleString(undefined, {minimumFractionDigits: 2});
            },
            toggleButton: function (e) {
                if (
                    e.target.parentElement.parentElement.parentElement.parentElement
                        .className == "n-box"
                ) {
                    e.target.parentElement.parentElement.parentElement.parentElement.className =
                        "n-box active";
                } else if (
                    e.target.parentElement.parentElement.parentElement.parentElement
                        .className == "n-box active"
                ) {
                    e.target.parentElement.parentElement.parentElement.parentElement.className =
                        "n-box";
                } else {
                    //
                }
            },
            removeReg: function (val) {
                const regExp = /[~!@#$%^&*()_+|'"<>?:{}]/;
                while (regExp.test(val)) {
                    val = val.replace(regExp, "");
                }
                return val;
            },
            calcTag: function (hashTag) {
                let rst = "";
                let tags = hashTag.split(",");
                for (let i in tags) {
                    rst =
                        rst +
                        "<span><button >" +
                        this.removeReg(tags[i]) +
                        "</button></span>";
                }
                return rst;
            },
        },
        computed: {
            paymentMethod() {
                if (this.order.cor_pay_type === '3D' || this.order.cor_pay_type === 'NOR') {
                    return 'creditCard'
                } else if (this.order.cor_pay_type === 'ABANK') {
                    return 'realtimeBankTransfer';
                } else if (this.order.cor_pay_type === 'paypal') {
                    return 'paypal';
                } else {
                    return '';
                }
            }
        }
    }
</script>

<style scoped="scoped" lang="scss">
    @import '/assets/plugins/slick/slick.css';
    @import '/assets/plugins/rangeSlider/css/ion.rangeSlider.min.css';

    .title-content {
        .n-box {
            >div {
                span {
                    margin: 0;
                }
            }
        }
    }

    .title-content {
        justify-content: space-between;

        .title {
            font-size: 14px;
        }

        .n-box {
            .n-flex {
                justify-content: space-between;
            }
        }
    }
</style>