import jwt from "../../http/requests/auth/jwt/index.js";
//import router from '@/router'

export default {
	updateUsername({
		commit
	}, payload) {
		payload.user.updateProfile({
			displayName: payload.displayName
		}).then(() => {

			// If username update is success
			let newUserData = Object.assign({}, payload.user.providerData[0]);
			newUserData.displayName = payload.displayName;
			commit('UPDATE_USER_INFO', newUserData, {
				root: true
			});
		}).catch((err) => {
			payload.notify({
				time: 8800,
				title: 'Error',
				text: err.message,
				iconPack: 'feather',
				icon: 'icon-alert-circle',
				color: 'danger'
			});
		});
	},
	// JWT
	loginJWT({
		commit
	}, payload) {
		return new Promise((resolve, reject) => {
			jwt.login(payload.userDetails.email, payload.userDetails.password, payload.checkbox_remember_me)
				.then(response => {
					if (response &&
						response.data &&
						response.data.userData &&
						response.data.accessToken
					) {
						commit('UPDATE_USER_INFO', response.data.userData, {
							root: true
						});
						commit("SET_BEARER", response.data.accessToken);
						resolve(response);
					} else {
						commit('REMOVE_USER_INFO', null, {
							root: true
						});
						commit("REMOVE_BEARER");
						reject({
							message: "Wrong email or password."
						});
					}
				})
				.catch(error => {
					commit('REMOVE_USER_INFO', null, {
						root: true
					});
					commit("REMOVE_BEARER");
					reject(error);
				});
		});
	},
	refreshJWTToken({
		commit,
		rootState
	}, payload) {
		return new Promise((resolve, reject) => {
			if (rootState.logoutAsked) {
				commit('REMOVE_USER_INFO', null, {
					root: true
				});
				commit("REMOVE_BEARER");
				reject({
					code: 401
				});
			} else {
				jwt.refreshToken(payload.token)
					.then(response => {
						if (response &&
							response.data &&
							response.data.userData &&
							response.data.accessToken &&
							!rootState.logoutAsked
						) {
							commit('UPDATE_USER_INFO', response.data.userData, {
								root: true
							});
							commit("SET_BEARER", response.data.accessToken);
							resolve(response);
						} else {
							commit('REMOVE_USER_INFO', null, {
								root: true
							});
							commit("REMOVE_BEARER");
							reject({
								code: 401
							});
						}
					})
					.catch(error => {
						if (error &&
							error.response &&
							error.response.status == 401
						) {
							commit('REMOVE_USER_INFO', null, {
								root: true
							});
							commit("REMOVE_BEARER");
						}
						reject(error);
					});
			}
		});
	},
	logoutJWT({
		commit,
		rootState
	}, payload) {
		return new Promise((resolve, reject) => {
			if (rootState.logoutAsked) {
				commit('REMOVE_USER_INFO', null, {
					root: true
				});
				commit("REMOVE_BEARER");
				resolve(true);
			} else {
				jwt.logout()
					.then(response => {
						if (response &&
							response.data &&
							response.data.loggedOut
						) {
							commit('SET_LOGOUT', null, {
								root: true
							});
							commit('REMOVE_USER_INFO', null, {
								root: true
							});
							commit("REMOVE_BEARER");
							resolve(response);
						} else if (response && response.data) {
							reject({
								message: response.data
							});
						} else {
							reject({
								message: 'Unable to logout.'
							});
						}
					})
					.catch(error => {
						if (error &&
							error.response &&
							(
								error.response.status == 428 ||
								error.response.status == 401
							)
						) {
							commit('SET_LOGOUT', null, {
								root: true
							});
							commit('REMOVE_USER_INFO', null, {
								root: true
							});
							commit("REMOVE_BEARER");
						}
						reject(error);
					});
			}
		});
	}

};
