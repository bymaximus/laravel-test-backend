import Vue from 'vue';
Vue.config.devtools = true;
Vue.config.performance = true;
Vue.config.productionTip = false;
Vue.prototype.$urlBasePath = '/';

import App from '@module/Dashboard.vue';

// Vuesax Component Framework
import Vuesax from 'vuesax';

Vue.use(Vuesax);

// axios
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



// API Calls
import "@/http/requests";

// Theme Configurations
import '@module/../themeConfig.js';


// ACL
import acl from '@/acl/acl';

// Globally Registered Components
import '@/globalComponents.js';

// Vue Router
import router from '@/router';

// Vuex Store
import store from '@/store/store';

// i18n
import i18n from '@/i18n/i18n';

// Vuesax Admin Filters
import '@/filters/filters';


import VeeValidate, {
	Validator
} from 'vee-validate';
import pt_BR from 'vee-validate/dist/locale/pt_BR';

Vue.use(VeeValidate, {
	inject: false
});
Validator.localize('pt_BR', pt_BR);


import VueLodash from 'vue-lodash';
import debounce from "lodash/debounce";
import cloneDeep from "lodash/cloneDeep";
Vue.use(VueLodash, {
	lodash: {
		debounce,
		cloneDeep
	}
});

// Vuejs - Vue wrapper for hammerjs
import {
	VueHammer
} from 'vue2-hammer';
Vue.use(VueHammer);

// PrismJS
import 'prismjs';
// import 'prismjs/themes/prism-tomorrow.css'

// Feather font icon
require('@assets/css/iconfont.css');

import "@fortawesome/fontawesome-free/css/all.css";
//import "@fortawesome/fontawesome-free/js/all.js";

import autofocus from "@vuejs-tips/v-autofocus";
Vue.use(autofocus);

new Vue({
	router,
	store,
	i18n,
	acl,
	render: h => h(App)
}).$mount('#app');
