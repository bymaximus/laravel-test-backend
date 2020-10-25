import axios from "../../../axios/index.js";

// Token Refresh
let subscribers = [];
var urlBasePath = '/';

function onAccessTokenFetched (access_token) {
	subscribers = subscribers.filter(callback => callback(access_token));
}

function addSubscriber (callback) {
	subscribers.push(callback);
}

export default {
	init () {
		axios.defaults.withCredentials = true;
		axios.interceptors.request.use((config) => {
			if (config &&
				(
					config.url == '/login' ||
					config.url == '/admin/login'
				) &&
				config.method == 'post' &&
				config.headers &&
				config.headers.common &&
				config.headers.common.hasOwnProperty('Authorization')
			) {
				delete config.headers.common['Authorization'];
			}
			return config;
		}, (error) => {
			return Promise.reject(error);
		});

		axios.interceptors.response.use((response) => {
			return response;
		}, (error) => {
			return Promise.reject(error);
		});
	},
	login (email, pwd, rememberMe) {
		return axios.post(urlBasePath + "login", {
			'email': email,
			'password': pwd,
			'rememberMe': rememberMe
		});
	},
	logout () {
		return axios.post(urlBasePath + "logout", {});
	},
	refreshToken (token) {
		var data = {
			token: token
		};
		return axios.post(urlBasePath + "refresh-login", data);
	}
};
