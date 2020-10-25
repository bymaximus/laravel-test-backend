<template>
	<div class="jwtMarginTop">
		<form @submit.prevent>
			<vs-input
				v-validate="'required|min:3'"
				data-vv-validate-on="blur"
				data-vv-as="Usuário"
				name="email"
				icon-no-border
				icon="icon icon-user"
				icon-pack="feather"
				label-placeholder="Usuário"
				:disabled="isBusy"
				v-model="email"
				class="w-full"
				ref="primeiroCampo"
				autocomplete="username"
				@keyup.enter.native="campoSenhaFocus"
			/>
			<span class="text-danger text-sm">{{ errors.first('email') }}</span>
			<vs-input
				data-vv-validate-on="blur"
				data-vv-as="Senha"
				v-validate="'required|min:3|max:50'"
				type="password"
				name="password"
				icon-no-border
				icon="icon icon-lock"
				icon-pack="feather"
				:disabled="isBusy"
				label-placeholder="Senha"
				v-model="password"
				class="w-full mt-8"
				ref="segundoCampo"
				autocomplete="current-password"
				@keyup.enter.native="loginJWT"
			/>
			<span class="text-danger text-sm">{{ errors.first('password') }}</span>
			<div class="flex flex-wrap justify-between my-5">
				<vs-checkbox
					v-model="checkbox_remember_me"
					:disabled="isBusy"
					class="mb-3"
				>Lembrar Credenciais</vs-checkbox>
			</div>
			<div class="mb-3">
				<vs-button
					class="float-right"
					:disabled="!validateForm"
					@click="loginJWT"
				>Entrar</vs-button>
			</div>
		</form>
	</div>
</template>

<script>
export default {
	data() {
		return {
			email: "",
			password: "",
			checkbox_remember_me: true,
			busy: true,
		};
	},
	computed: {
		validateForm() {
			return (
				!this.errors.any() &&
				this.email != "" &&
				this.password != "" &&
				!this.busy
			);
		},
		isBusy() {
			return this.busy;
		},
	},
	methods: {
		campoSenhaFocus() {
			this.$nextTick(() => {
				setTimeout(() => {
					window
						.jQuery(this.$refs.segundoCampo.$refs.vsinput)
						.trigger("focus");
				}, 150);
			});
		},
		loginJWT() {
			if (this.busy || !this.validateForm) {
				return;
			}
			this.busy = true;
			if (this.$store.getters.isUserLoggedIn) {
				this.$vs.notify({
					title: "Autenticação",
					text: "Você já está autenticado!",
					iconPack: "feather",
					icon: "icon-alert-circle",
					color: "warning",
				});
				setTimeout(() => {
					document.location.href = '/dashboard';
				}, 1000);
				return;
			}

			this.$vs.loading({
				text: "Carregando...",
			});

			const payload = {
				checkbox_remember_me: this.checkbox_remember_me,
				userDetails: {
					email: this.email,
					password: this.password,
				},
			};
			this.$store.dispatch("auth/loginJWT", payload)
				.then((response) => {
					this.$vs.loading.close();
					this.$vs.notify({
						title: "Acesso Permitido",
						text: "Redirecionando...",
						iconPack: "feather",
						icon: "icon-user",
						color: "success",
					});
					setTimeout(() => {
						document.location.href = '/dashboard';
					}, 1000);
				})
				.catch((error) => {
					this.$vs.loading.close();
					var errMsg = "Erro na requisição.";
					if (
						error &&
						error.response &&
						error.response.data &&
						error.response.data.error
					) {
						errMsg = error.response.data.error;
					} else if (
						error &&
						error.response &&
						error.response.data &&
						error.response.data.message
					) {
						errMsg = error.response.data.message;
					} else if (error && error.message) {
						errMsg = error.message;
					}
					this.$vs.notify({
						title: "Erro",
						text: errMsg,
						iconPack: "feather",
						icon: "icon-alert-circle",
						color: "danger",
					});
					if (
						error &&
						error.response &&
						(error.response.status == 428 ||
							error.response.status == 409)
					) {
						setTimeout(() => {
							document.location.reload();
						}, 1500);
					} else {
						this.busy = false;
					}
				});
		},
	},
	mounted() {
		if (this.$store.getters.isUserLoggedIn) {
			this.$store.commit("auth/SET_BEARER", this.$store.state.AppActiveUser.token);
			this.busy = true;
			this.$vs.loading();
			this.$store.dispatch("auth/refreshJWTToken", {
					token: this.$store.state.AppActiveUser.token,
				})
				.then((response) => {
					this.$vs.loading.close();
					this.$vs.notify({
						title: "Acesso Automático",
						text: "Redirecionando...",
						iconPack: "feather",
						icon: "icon-user",
						color: "success",
					});
					setTimeout(() => {
						document.location.href = '/dashboard';
					}, 1000);
				})
				.catch((error) => {
					this.$vs.loading.close();
					var errMsg = "Erro na requisição.";
					if (
						error &&
						error.response &&
						error.response.data &&
						error.response.data.error
					) {
						errMsg = error.response.data.error;
					} else if (
						error &&
						error.response &&
						error.response.data &&
						error.response.data.message
					) {
						errMsg = error.response.data.message;
					} else if (error && error.message) {
						errMsg = error.message;
					}
					this.$vs.notify({
						title: "Erro no Acesso Automático",
						text: errMsg,
						iconPack: "feather",
						icon: "icon-alert-circle",
						color: "danger",
					});
					if (
						error &&
						((error.response &&
							(error.response.status == 401 ||
								error.response.status == 500)) ||
							error.code == 401 ||
							error.code == 500)
					) {
						setTimeout(() => {
							document.location.reload();
						}, 1500);
					} else {
						this.busy = false;
						this.$nextTick(() => {
							setTimeout(() => {
								window
									.jQuery(
										this.$refs.primeiroCampo.$refs.vsinput
									)
									.trigger("focus");
							}, 150);
						});
					}
				});
		} else {
			this.$store.commit("auth/REMOVE_BEARER");
			this.$store.commit("REMOVE_USER_INFO");
			this.busy = false;
			this.$nextTick(() => {
				setTimeout(() => {
					window
						.jQuery(this.$refs.primeiroCampo.$refs.vsinput)
						.trigger("focus");
				}, 150);
			});
		}
	},
};
</script>

<style lang="scss">
.jwtMarginTop {
	margin-top: 5rem;
}
</style>
