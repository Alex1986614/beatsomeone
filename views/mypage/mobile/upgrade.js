import Vue from "vue";
import Vuex from "vuex";
Vue.use(Vuex);
import VueRouter from "vue-router";
import "bootstrap";

import i18n from '*/src/i18n/i18n';
import app from "./Upgrade.vue";
import SelectGroup from "./pages/SelectGroup.vue";
import PayGroup from "./pages/PayGroup.vue";

Vue.use(VueRouter);

const router = new VueRouter({
    routes: [
        {path: '/', component: SelectGroup},
        {path: '/pay', component: PayGroup},
    ],
});

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
    router,
    store,
    render: (h) => h(app),
}).$mount('#app');