
import Vue from 'vue';
Vue.config.devtools = true;
Vue.config.performance = true;
Vue.config.productionTip = false;
Vue.prototype.$urlBasePath = '/';

import App from '@module/Login.vue';

import Vuesax from 'vuesax';

Vue.use(Vuesax);

import axios from "@/axios.js";
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
	axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
	console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
Vue.prototype.$http = axios;

import jQuery from "jquery";
window.$ = jQuery;
window.jQuery = jQuery;

import "@/http/requests";

import '@module/../themeConfig.js';

import '@/globalComponents.js';

import router from '@/loginRouter';

import store from '@/store/store';

import i18n from '@/i18n/i18n';

import '@/filters/filters';

import VeeValidate, {
	Validator
} from 'vee-validate';
import pt_BR from 'vee-validate/dist/locale/pt_BR';

Vue.use(VeeValidate);
Validator.localize('pt_BR', pt_BR);

import 'prismjs';

require('@assets/css/iconfont.css');

new Vue({
	router,
	store,
	i18n,
	render: h => h(App)
}).$mount('#app');
