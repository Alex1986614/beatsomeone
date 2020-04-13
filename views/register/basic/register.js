import Vue from 'vue'
import VueRouter  from 'vue-router';
import app from './Register.vue';


import Register_1 from "./Register_1";
import Register_2 from "./Register_2";
import Register_3 from "./Register_3";
import Register_4 from "./Register_4";
import Register_5 from "./Register_5";
import Register_6 from "./Register_6";
Vue.use(VueRouter);


Vue.config.productionTip = false;
Vue.prototype.$log = console.log.bind(console);

const router = new VueRouter({
  routes: [
    { path: '/', component: Register_1},
    { path: '/2', component: Register_2},
    { path: '/3', component: Register_3},
    { path: '/4', component: Register_4},
    { path: '/5', component: Register_5},
    { path: '/6', component: Register_6},
  ],
  scrollBehavior (to, from, savedPosition) {
    return { x: 0, y: 0 }
  }
});


window.vm = new Vue({
  router,
  render: h => h(app),
}).$mount('#app')