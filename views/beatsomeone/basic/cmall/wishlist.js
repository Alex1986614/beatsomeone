import Vue from 'vue'

import app from './Wishlist.vue';
// i18n
import i18n from '*/src/i18n/i18n'

Vue.config.productionTip = false;
Vue.prototype.$log = console.log.bind(console);


window.vm = new Vue({
  i18n,
  render: h => h(app),
}).$mount('#app')