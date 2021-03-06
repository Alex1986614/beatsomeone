import Vue from 'vue';
import Vuex from 'vuex';

import app from './Beatsomeone.vue';
// i18n
import i18n from '*/src/i18n/i18n'

Vue.config.productionTip = false;
Vue.prototype.$log = console.log.bind(console);
Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        cartSum: 0,
        cartSumD: 0,
    },
    mutations: {
        ADD_MONEY(state, payload) {
            state.cartSum = state.cartSum + payload.money;
            state.cartSumD = state.cartSumD + payload.money_d;
        }
    },
    getters: {
        getCartSum(state) {
            return state.cartSum;
        },
        getCartSumD(state) {
            return state.cartSumD;
        }
    },
    actions: {
        // moneyObject: {money: 0, money_d: 0}
        addMoney(context, moneyObject) {
            context.commit('ADD_MONEY', moneyObject);
        }
    }
});

window.vm = new Vue({
    i18n,
    store,
    render: h => h(app),
}).$mount('#app')
