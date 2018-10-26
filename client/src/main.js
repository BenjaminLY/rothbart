// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import store from './store'

// Import Axios
import axios from 'axios'

// Import VueAxios
import VueAxios from 'vue-axios'

// Import Buefy
import Buefy from 'buefy'
import 'buefy/lib/buefy.css'

// Import Bulma Extension
import 'bulma-extensions/dist/bulma-extensions.min.css'

// Import fontawesome stylesheets
import './assets/fontawesome/scss/fontawesome.scss'
import './assets/fontawesome/scss/fa-regular.scss'
import './assets/fontawesome/scss/fa-solid.scss'

import './assets/scss/main.scss'

// Init Axios Vue Plugin
Vue.use(VueAxios, axios)

// Init Buefy
Vue.use(Buefy)

Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
    el: '#app',
    router,
    store,
    components: { App },
    template: '<App/>'
})
