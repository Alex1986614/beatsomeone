<template>
    <li class="playList__itembox">
        <div class="playList__item playList__item--title other">
            <div class="n-flex h-center">
                <div class="col name">
                    <figure>
                            <span class="playList__cover">
                              <img
                                  v-if="!item.item.cit_file_1"
                                  :src="'/assets/images/cover_default.png'"
                                  alt
                              />
                              <img v-else :src="'/uploads/cmallitem/' + item.item.cit_file_1" alt />
                              <i v-if="item.item.is_new" class="label new">N</i>
                            </span>
                        <figcaption class="pointer">
                            <h3 class="playList__title">{{ truncate(item.item.cit_name, 20) }}</h3>
                            <span class="playList__by">by {{item.item.mem_nickname}}</span>
                        </figcaption>
                    </figure>
                </div>
                <div class="edit" style="text-align: right">
                    <div class="price 11221122" style="margin-top: 5px; font-size: 16px; font-weight: bold">
                        {{ formatPrice(item.itemdetail[0].cde_price, item.itemdetail[0].cde_price_d, true) }}
                    </div>
                </div>
            </div>
            <div class="n-flex">
                <div class="col n-option">
                    <!-- Option -->
                    <div class="option basic" v-if="item.itemdetail[0].cde_title === 'LEASE'">
                        <div class="n-box">
                            <div>
                                <button class="playList__item--button">
                                  <span class="option_fold">
                                    <img
                                        src="/assets/images/icon/togglefold.png"
                                        @click.self="toggleButton"
                                    />
                                  </span>
                                    <div>
                                        <div class="title" style="font-size: 12px;" @click.self="toggleButton">
                                            {{ $t('lang23') }}
                                        </div>
                                        <div class="detail">{{ $t('lang24') }}</div>
                                    </div>
                                </button>
                                <div class="option_item basic">
                                    <div class="purchase-description">
                                        <p>
                                            <i>
                                                <img src="/assets/images/icon/parchase-info6.png" alt/>
                                            </i>
                                            {{$t('lang25')}}
                                        </p>
                                        <p></p>
                                        <p>
                                            <i>
                                                <img src="/assets/images/icon/parchase-info1.png" alt/>
                                            </i>
                                            {{$t('lang26')}}
                                        </p>
                                        <p>
                                            <i>
                                                <img src="/assets/images/icon/parchase-info3.png" alt/>
                                            </i>
                                            {{$t('lang27')}}
                                        </p>
                                        <p>
                                            <i>
                                                <img src="/assets/images/icon/parchase-info2.png" alt/>
                                            </i>
                                            {{$t('lang28')}}
                                        </p>
                                        <p>
                                            <i>
                                                <img src="/assets/images/icon/parchase-info7.png" alt/>
                                            </i>
                                            {{$t('lang29')}}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="option unlimited" v-else-if="item.itemdetail[0].cde_title === 'STEM'">
                        <div class="n-box">
                            <div>
                                <button class="playList__item--button">
                                  <span class="option_fold">
                                    <img
                                        src="/assets/images/icon/togglefold.png"
                                        @click.self="toggleButton"
                                    />
                                  </span>
                                    <div>
                                        <div class="title" style="font-size: 12px;" @click.self="toggleButton">
                                            {{ $t('lang30') }}
                                        </div>
                                        <div class="detail">{{ $t('lang31') }}
                                            <span class="copytransfer" v-if="item.item.cit_include_copyright_transfer === '1'">{{ $t('lang32') }}</span>
                                        </div>
                                    </div>
                                </button>
                                <div class="option_item unlimited">
                                    <div class="purchase-description">
                                        <p>
                                            <i>
                                                <img src="/assets/images/icon/parchase-info6.png" alt/>
                                            </i>
                                            {{ $t('lang33') }}
                                        </p>
                                        <p>
                                            <i>
                                                <img src="/assets/images/icon/parchase-info8.png"/>
                                            </i>
                                            {{$t('lang34')}}
                                        </p>
                                        <p>
                                            <i>
                                                <img src="/assets/images/icon/parchase-info9.png"/>
                                            </i>
                                            {{$t('lang35')}}
                                        </p>
                                        <p>
                                            <i>
                                                <img src="/assets/images/icon/parchase-info4.png"/>
                                            </i>
                                            {{$t('lang36')}}
                                        </p>
                                        <p v-if="item.item.cit_include_copyright_transfer !== '1'">
                                            <i>
                                                <img src="/assets/images/icon/parchase-info10.png"/>
                                            </i>
                                            {{$t('lang41')}}
                                        </p>
                                        <p v-else>
                                            <i>
                                                <img src="/assets/images/icon/parchase-info10.png"/>
                                            </i>
                                            {{$t('lang42')}}
                                        </p>

                                        <div class="copybox" v-if="item.item.cit_include_copyright_transfer !== '1'">
                                            <span>{{ $t('lang21') }}</span>
                                            <span>{{ $t('lang22') }}</span>
                                        </div>
                                        <div class="copybox" v-else>
                                            <span>{{ $t('lang43') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </li>
</template>

<script>
import ItemDetail from "../component/ItemDetail";

export default {
    name: "OrderItem",
    props: ['item', 'pg'],
    components: {
        ItemDetail
    },
    mounted() {
        console.log(this.item);
    },
    methods: {
        truncate(str, n) {
            return (str.length > n) ? str.substr(0, n-1) + '...' : str;
        },
        formatPrice: function (kr, en) {
            if (this.pg === 'paypal') {
                return '$ ' + Number(en).toLocaleString(undefined, {minimumFractionDigits: 2});
            } else if (this.pg === 'allat') {
                return '₩ ' + Number(kr).toLocaleString('ko-KR', {minimumFractionDigits: 0});
            } else {
                return '₩ ' + Number(kr).toLocaleString('ko-KR', {minimumFractionDigits: 0});
            }
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
    }
}
</script>

<style lang="scss" scoped>
.purchase-description p {
    margin-bottom: 5px !important;
    display: flex;
    align-items: flex-start;
    font-size: 14px;
    line-height: 20px;

    i {
        flex: none;
        margin-right: 10px;
        width: 20px;
        text-align: center;
        line-height: 20px;
        height: 20px;
    }
}
</style>