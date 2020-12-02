import Vue from 'vue'
import VueRouter from 'vue-router';
import Vuex from "vuex";

Vue.use(Vuex);

import app from './Register.vue';
// i18n
import i18n from '*/src/i18n/i18n'

import Register_1 from "./Register_1";
import Register_2 from "./Register_2";
import Register_3 from "./Register_3";
import Register_4 from "./Register_4";
import Register_5 from "./Register_5";
import PurchaseMembership from "./PurchaseMembership";

Vue.use(VueRouter);


Vue.config.productionTip = false;
Vue.prototype.$log = console.log.bind(console);

const router = new VueRouter({
    routes: [
        {path: '/', component: Register_1},
        {path: '/2', component: Register_2},
        {path: '/3', component: Register_3},
        {path: '/4', component: Register_4},
        {path: '/5', component: Register_5},
    ],
    scrollBehavior(to, from, savedPosition) {
        return {x: 0, y: 0}
    }
});

const store = new Vuex.Store({
    state: {
        cartSum: 0,
        cartSumD: 0,
        userInfo: {},
        userOffer: 'buyer'
    },
    mutations: {
        ADD_MONEY(state, payload) {
            state.cartSum = state.cartSum + payload.money;
            state.cartSumD = state.cartSumD + payload.money_d;
        },
        SET_USER_INFO(state, payload) {
            state.userInfo = payload
        },
        SET_USER_Offer(state, offer){
            state.userOffer = offer
        }
    },
    getters: {
        getCartSum(state) {
            return state.cartSum;
        },
        getCartSumD(state) {
            return state.cartSumD;
        },
        getUserInfo(state) {
          return state.userInfo;
        },
        getUserOffer(state) {
            return state.userOffer;
        }
    },
    actions: {
        // moneyObject: {money: 0, money_d: 0}
        addMoney(context, moneyObject) {
            context.commit('ADD_MONEY', moneyObject);
        },
        setUserInfo(context, userInfo) {
            context.commit('SET_USER_INFO', userInfo);
        },
        setUserOffer(context, userOffer) {
            context.commit('SET_USER_Offer', userOffer);
        }
    }
});


window.vm = new Vue({
    i18n,
    router,
    store,
    render: h => h(app),
}).$mount('#app')
