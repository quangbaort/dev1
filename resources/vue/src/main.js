import '@/plugins/vue-composition-api'
import '@/styles/styles.scss'
import Vue from 'vue'
import App from './App.vue'
import './plugins/acl'
import vuetify from './plugins/vuetify'
import router from './router'
import store from './store'
import { lang } from '@/common'
import 'v-contextmenu/dist/index.css'
import loader from "vue-ui-preloader";
Vue.prototype.$trans = lang
Vue.config.productionTip = false
Vue.use(loader)
new Vue({
  el : '#app',
  router,
  store,
  vuetify,
  render: h => h(App),
})

