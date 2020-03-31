import Vue from 'vue'

import app from './Login.vue';


Vue.config.productionTip = false;
Vue.prototype.$log = console.log.bind(console);


window.vm = new Vue({
  render: h => h(app),
}).$mount('#app')
