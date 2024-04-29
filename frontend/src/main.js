import './assets/css/satoshi.css'
import './assets/css/style.css'
import 'jsvectormap/dist/css/jsvectormap.min.css'
import 'flatpickr/dist/flatpickr.min.css'
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { fas } from "@fortawesome/free-solid-svg-icons";

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import VueApexCharts from 'vue3-apexcharts'

import App from './App.vue'
import router from './router'
import { authStore } from '@/stores/auth'
import axios from "axios";
import dialog from './libs/dialog';
import helpers from './libs/helpers';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

const app = createApp(App)
library.add(fas)
window.axios = axios;
window.dialog = dialog;
window.helpers = helpers;
app.use(pinia)
app.use(router)
app.use(VueApexCharts)
app.component('fa', FontAwesomeIcon)
const auth_store = authStore()
app.config.globalProperties.$filters = {
    can(permission) {
        return auth_store.user.permissions.some(userPermission => userPermission.name  == permission)
      }
}

app.mount('#app')
