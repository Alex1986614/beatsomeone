<template>
    <li class="playList__itembox">
        <div class="playList__item playList__item--title other">
            <div class="col check">
                <label class="checkbox">
                    <input
                        type="checkbox"
                        hidden="hidden"
                        :value="item.cit_id"
                        v-model="checked"
                    />
                    <span></span>
                </label>
            </div>
            <div class="col name">
                <figure>
                    <div class="playList__cover">
                        <img :src="(!item.thumb) ? '/assets/images/cover_default.png' : '/uploads/cmallitem/' + item.thumb" alt/>
                        <i v-if="item.is_new" class="label new">N</i>
                    </div>
                    <figcaption class="pointer">
                        <h3 class="playList__title">{{
                                formatCitName(item.cit_name)
                            }}</h3>
                        <span class="playList__by">by {{ item.mem_nickname }}</span>
                    </figcaption>
                </figure>
            </div>
            <div class="col n-option">
                <!-- Option -->
                <div class="option">
                    <div class="n-box" v-if="item.detail[0].cde_title === 'LEASE'">
                        <div>
                            <button class="playList__item--button">
                                <span class="option_fold">
                                  <img
                                      src="/assets/images/icon/togglefold.png"
                                      @click.self="toggleButton"
                                  />
                                </span>
                                <div>
                                    <div
                                        class="title"
                                        @click.self="toggleButton"
                                    >{{ $t('lang23') }}
                                    </div>
                                    <div class="detail">{{ $t('lang24') }}</div>
                                </div>
                                <div
                                    class="price"
                                >{{
                                        formatPrice(item.detail[0].cde_price, item.detail[0].cde_price_d)
                                    }}
                                </div>
                            </button>
                            <ParchaseComponent :item="item"
                                               :type="'basic'"></ParchaseComponent>
                        </div>
                    </div>
                    <div class="n-box" v-else-if="item.detail[0].cde_title === 'STEM'">
                        <div>
                            <button class="playList__item--button">
                                <span class="option_fold">
                                  <img
                                      src="/assets/images/icon/togglefold.png"
                                      @click.self="toggleButton"
                                  />
                                </span>
                                <div>
                                    <div
                                        class="title"
                                        @click.self="toggleButton"
                                    >{{ $t('lang30') }}
                                    </div>
                                    <div class="detail">{{ $t('lang31') }}
                                        <span class="copytransfer" v-if="item.cit_include_copyright_transfer === '1'">{{ $t('lang32') }}</span>
                                    </div>
                                </div>
                                <div
                                    class="price"
                                >{{
                                        formatPrice(item.detail[0].cde_price, item.detail[0].cde_price_d)
                                    }}
                                </div>
                            </button>
                            <ParchaseComponent :item="item"
                                               :type="'mastering'"></ParchaseComponent>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="col feature">
                                      <div class="price" v-if="item.detail[0].cit_lease_license_use === '1'">
                                          {{ formatPrice(item.detail[0].cde_price, item.detail[0].cde_price_d) }}
                                      </div>
                                      <div class="price" v-if="item.detail[0].cit_mastering_license_use === '1'" >
                                          {{ formatPrice(item.detail[0].cde_price_2, item.detail[0].cde_price_d_2) }}
                                      </div>
            </div>-->
            <div class="col edit">
                <button
                    class="btn btn--blue round"
                    style="height:40px; padding:0 16px;"
                    @click="goBuy()"
                >{{ $t('buyNow') }}
                </button>
            </div>
        </div>
    </li>
</template>

<script>
import ParchaseComponent from "../component/Parchase";
import axios from "axios";

export default {
    name: "CartItem",
    props: ['item', "value"],
    components: {
        ParchaseComponent
    },
    computed: {
        checked: {
            get() {
                return this.value;
            },
            set(val) {
                this.$emit('input', val);
            }
        }
    },
    methods: {
        formatCitName: function (data) {
            var rst;
            var limitLth = 50;
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
        formatPrice: function (kr, en) {
            if (this.$i18n.locale === "en") {
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
        goBuy: function () {
            let formData = new FormData();
            formData.append('chk[]', this.item.cit_id);
            axios.post('/cmall/ajax_orderstart', formData)
                .then(res => res.data)
                .then(data => {
                    window.location.href = '/cmall/billing';
                })
                .catch(error => {
                    console.error(error.response);
                })
        },
        toggleButton: function (e) {
            if (
                e.target.parentElement.parentElement.parentElement.parentElement
                    .className === "n-box"
            ) {
                e.target.parentElement.parentElement.parentElement.parentElement.className =
                    "n-box active";
            } else if (
                e.target.parentElement.parentElement.parentElement.parentElement
                    .className === "n-box active"
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
.playList__item--button {
    display: flex;
    flex-direction: row;
    color: white;
    text-align: left;
}

.playList__item .n-option .n-box:first-child .price {
    margin-top: 0;
}

.playList__item.other .active .option_item.unlimited {
    height: 140px;
}

.n-box.active {
    margin-bottom: 0;
}

.playList__item .n-option .n-box .price {
    color: white;
}
</style>