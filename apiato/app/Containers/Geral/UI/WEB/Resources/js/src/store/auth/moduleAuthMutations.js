import axios from "../../http/axios/index.js";

export default {
	SET_BEARER (state, accessToken) {
		if (accessToken) {
			axios.defaults.headers.common['Authorization'] = 'Bearer ' + accessToken;
		}
	},
	REMOVE_BEARER (state, payload) {
		if (axios &&
			axios.defaults &&
			axios.defaults.headers &&
			axios.defaults.headers.common &&
			axios.defaults.headers.common.hasOwnProperty('Authorization')
		) {
			axios.defaults.headers.common['Authorization'] = '';
			delete axios.defaults.headers.common['Authorization'];
		}
	}
};
