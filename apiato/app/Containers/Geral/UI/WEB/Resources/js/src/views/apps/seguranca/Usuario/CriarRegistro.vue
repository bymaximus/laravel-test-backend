<template>
	<div :class="configuracaoPagina.nomeEstilo + 'Criar'">
		<div class="vx-card p-6">
			<vs-alert
				:active="(mensagemErro && mensagemErro != '' ? true : false)"
				color="danger"
				class="mb-10"
			>
				<span v-html="mensagemErro"></span>
			</vs-alert>
			<form @submit.prevent>
				<div class="vx-row mb-3">
					<div class="vx-col w-full">
						<vs-input
							name="usuario"
							label="Usuário"
							v-model="camposCriar.usuario"
							class="w-full campoRequerido"
							v-validate="'required|min:1|max:100'"
							val-icon-danger="clear"
							val-icon-success="done"
							data-vv-as="Usuário"
							:danger="errors.has('usuario')"
							:success="!errors.has('usuario') && camposCriar.usuario != ''"
							:color="!errors.has('usuario') ? 'success' : 'danger'"
							:disabled="backendOcupado"
							ref="primeiroCampo"
						/>
						<span
							class="text-danger text-sm mt-2"
							v-show="errors.has('usuario')"
							:class="[
                                    {'block' : errors.has('usuario')}
                                ]"
						>{{ errors.first('usuario') }}</span>
					</div>
				</div>
				<div class="vx-row mb-3">
					<div class="vx-col w-full">
						<vs-input
							name="senha"
							label="Senha"
							v-model="camposCriar.senha"
							class="w-full campoRequerido"
							v-validate="'required|min:3|max:100'"
							val-icon-danger="clear"
							val-icon-success="done"
							data-vv-as="Senha"
							:danger="errors.has('senha')"
							:success="!errors.has('senha') && camposCriar.senha != ''"
							:color="!errors.has('senha') ? 'success' : 'danger'"
							:disabled="backendOcupado"
							ref="senha"
						/>
						<span
							class="text-danger text-sm mt-2"
							v-show="errors.has('senha')"
							:class="[
                                    {'block' : errors.has('senha')}
                                ]"
						>{{ errors.first('senha') }}</span>
					</div>
				</div>
				<div class="vx-row mb-3">
					<div class="vx-col w-full">
						<vs-input
							name="senha_confirmacao"
							label="Confirmação de Senha"
							v-model="camposCriar.senhaConfirmacao"
							class="w-full campoRequerido"
							v-validate="'required|min:3|max:100|confirmed:senha'"
							val-icon-danger="clear"
							val-icon-success="done"
							data-vv-as="Confirmação de Senha"
							:danger="errors.has('senha_confirmacao')"
							:success="!errors.has('senha_confirmacao') && camposCriar.senhaConfirmacao != ''"
							:color="!errors.has('senha_confirmacao') ? 'success' : 'danger'"
							:disabled="backendOcupado"
							@keyup.enter.native="processarCriar"
						/>
						<span
							class="text-danger text-sm mt-2"
							v-show="errors.has('senha_confirmacao')"
							:class="[
                                    {'block' : errors.has('senha_confirmacao')}
                                ]"
						>{{ errors.first('senha_confirmacao') }}</span>
					</div>
				</div>
				<div class="vx-row">
					<div class="vx-col w-full">
						<label
							for="id_perfil"
							class="vs-select--label campoRequerido"
							:class="[
								{'input-select-label-success--active' : !errors.has('id_perfil') && camposCriar.idPerfil && camposCriar.idPerfil != '' && campoIdPerfilFocado},
								{'input-select-label-danger--active' : errors.has('id_perfil')}
							]"
						>Perfil</label>
						<v-select
							name="id_perfil"
							label="nome"
							placeholder="Perfil"
							v-model="camposCriar.idPerfil"
							class="w-full"
							v-validate="{ required: true, included: listaPerfis.map(s => s.id) }"
							data-vv-as="Perfil"
							:filterable="false"
							:searchable="false"
							:reduce="item => item.id"
							:options="listaPerfis"
							:disabled="backendOcupado"
							:loading="obtendoListas"
							@search:focus="campoIdPerfilFocado = true"
							@search:blur="campoIdPerfilFocado = false"
						>
							<div slot="no-options">Nenhum registro disponível.</div>
						</v-select>
						<span
							class="text-danger text-sm mt-2"
							v-show="errors.has('id_perfil')"
							:class="[
                                    {'block' : errors.has('id_perfil')}
                                ]"
						>{{ errors.first('id_perfil') }}</span>
					</div>
				</div>
				<div class="vx-row mb-3 mt-10">
					<div class="vx-col w-full sm:w-1/2">
						<vs-button
							color="warning"
							type="filled"
							:disabled="backendOcupado"
							@click="cancelar"
						>Cancelar</vs-button>
					</div>
					<div class="vx-col w-full sm:w-1/2">
						<vs-button
							color="primary"
							type="filled"
							class="float-right"
							:disabled="backendOcupado || formularioCriarInvalido"
							@click="processarCriar"
						>Criar {{ backendModel }}</vs-button>
					</div>
				</div>
			</form>
		</div>
	</div>
</template>

<script>
import axios from "@/axios.js";
import vSelect from "vue-select";

export default {
	$_veeValidate: {
		validator: "new",
	},
	props: {
		abaAtiva: {
			type: Number,
			default: null,
		},
	},
	components: {
		"v-select": vSelect,
	},
	data() {
		return {
			configuracaoPagina: require("./configs.js"),
			backendOcupado: false,
			obtendoListas: false,
			listaPerfis: [],
			campoIdPerfilFocado: false,
			mensagemErro: "",
			camposCriar: {
				usuario: "",
				senha: "",
				senhaConfirmacao: "",
				idPerfil: null,
			},
		};
	},
	computed: {
		backendUrl() {
			return this.$urlBasePath + this.configuracaoPagina.backendUrl;
		},
		backendModel() {
			return this.configuracaoPagina.backendModel;
		},
		formularioCriarInvalido() {
			var invalid = this.errors.any();
			if (!invalid && this.fields) {
				invalid = Object.keys(this.fields).some((key) => {
					if (this.fields[key].invalid) {
						return true;
					} else if (!this.fields[key].valid) {
						return true;
					}
				});
			}
			return invalid;
		},
		camposFormularioCriar() {
			return {
				usuario: this.camposCriar.usuario,
				senha: this.camposCriar.senha,
				id_perfil: this.camposCriar.idPerfil,
			};
		},
	},
	mounted() {
		if (!this.$acl.check("modificar")) {
			this.backendOcupado = false;
			this.cancelar();
		} else {
			this.backendOcupado = true;
			this.obtendoListas = true;
			axios
				.get(this.backendUrl + "/perfil")
				.then((response) => {
					if (
						response &&
						response.status == 200 &&
						response.data &&
						response.data instanceof Object
					) {
						if (
							response.data.hasOwnProperty("perfis") &&
							response.data.perfis &&
							response.data.perfis instanceof Array
						) {
							this.listaPerfis = response.data.perfis;
						} else {
							this.$vs.notify({
								title: "Erro",
								text: "Incapaz de carregar a lista de perfis.",
								iconPack: "feather",
								icon: "icon-alert-circle",
								color: "danger",
							});
						}
					} else {
						this.$vs.notify({
							title: "Erro",
							text: "Incapaz de carregar a lista de perfis.",
							iconPack: "feather",
							icon: "icon-alert-circle",
							color: "danger",
						});
					}
					this.obtendoListas = false;
					this.backendOcupado = false;
					this.$nextTick(() => {
						this.$refs.primeiroCampo.$el.focus();
					});
				})
				.catch((error) => {
					var errMsg = "Erro na requisição.";
					if (
						error &&
						error.response &&
						error.response.data &&
						error.response.data.message
					) {
						errMsg = error.response.data.message;
					} else if (
						error &&
						error.response &&
						error.response.data &&
						error.response.data.error
					) {
						errMsg = error.response.data.error;
					} else if (error && error.message) {
						errMsg = error.message;
					}
					if (
						error &&
						error.response &&
						error.response.status == 401
					) {
						this.$vs.notify({
							title: "Erro",
							text: errMsg,
							iconPack: "feather",
							icon: "icon-alert-circle",
							color: "danger",
							fixed: true,
						});
						setTimeout(() => {
							document.location.reload();
						}, 1500);
						return;
					}
					this.$vs.notify({
						title: "Erro",
						text: errMsg,
						iconPack: "feather",
						icon: "icon-alert-circle",
						color: "danger",
					});
					this.obtendoListas = false;
					this.backendOcupado = false;
				});
		}
	},
	methods: {
		cancelar() {
			if (this.backendOcupado) {
				return;
			}
			this.backendOcupado = true;
			this.$router.push(this.configuracaoPagina.routerPath);
		},
		processarCriar() {
			if (this.backendOcupado) {
				return;
			}
			this.mensagemErro = "";
			this.backendOcupado = true;
			this.$validator.validate().then((result) => {
				if (result) {
					axios
						.post(this.backendUrl, this.camposFormularioCriar)
						.then((response) => {
							if (
								response &&
								response.status == 201 &&
								response.data &&
								response.data.status == "criado"
							) {
								this.$vs.notify({
									title: "Sucesso",
									text: `${this.backendModel} criado.`,
									iconPack: "feather",
									icon: "icon-check",
									color: "success",
								});
								setTimeout(() => {
									this.$router.push(
										this.configuracaoPagina.routerPath
									);
								}, 1000);
							} else {
								this.$vs.notify({
									title: "Erro",
									text:
										"Dados de resposta do registro criado inválidos.",
									iconPack: "feather",
									icon: "icon-alert-circle",
									color: "danger",
								});
								this.backendOcupado = false;
							}
						})
						.catch((error) => {
							var errMsg = "Erro na requisição.";
							if (
								error &&
								error.response &&
								error.response.data &&
								error.response.data.message
							) {
								errMsg = error.response.data.message;
							} else if (
								error &&
								error.response &&
								error.response.data &&
								error.response.data.error
							) {
								errMsg = error.response.data.error;
							} else if (error && error.message) {
								errMsg = error.message;
							}
							if (
								error &&
								error.response &&
								error.response.status == 401
							) {
								this.$vs.notify({
									title: "Erro",
									text: errMsg,
									iconPack: "feather",
									icon: "icon-alert-circle",
									color: "danger",
									fixed: true,
								});
								setTimeout(() => {
									document.location.reload();
								}, 1500);
								return;
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
								error.response.data &&
								error.response.data.errors
							) {
								if (
									error.response.data.errors instanceof
										Array ||
									error.response.data.errors instanceof Object
								) {
									var erros = {};
									Object.keys(
										this.camposFormularioCriar
									).forEach((key) => {
										if (
											error.response.data.errors.hasOwnProperty(
												key
											) &&
											error.response.data.errors[key] &&
											error.response.data.errors[key]
												.length > 0 &&
											error.response.data.errors[key][0]
										) {
											erros[key] = true;
											this.errors.add({
												field: key,
												msg:
													error.response.data.errors[
														key
													][0],
											});
										}
									});
									Object.keys(
										error.response.data.errors
									).forEach((key) => {
										if (
											error.response.data.errors[key] &&
											error.response.data.errors[key]
												.length > 0 &&
											error.response.data.errors[
												key
											][0] &&
											(!erros ||
												!erros.hasOwnProperty(key) ||
												!erros[key])
										) {
											erros[key] = true;
											this.mensagemErro +=
												error.response.data.errors[
													key
												][0].toString() + "<br/>";
										}
									});
								} else {
									this.mensagemErro = error.response.data.errors.toString();
								}
							}
							this.backendOcupado = false;
						});
				} else {
					this.backendOcupado = false;
				}
			});
		},
	},
};
</script>
<style lang="scss">
@import "./configs.scss";

.pagina#{$nomePagina}Criar {
	ul.vs__dropdown-menu {
		max-height: 90px;
	}
	div.vs-input.campoRequerido > label,
	label.campoRequerido {
		font-weight: 500;
	}
	div.vs-input.campoRequerido > label::after,
	label.campoRequerido::after {
		content: " *";
		color: red;
	}
	.vs-select--label:not(.input-select-label-danger--active):not(.input-select-label-success--active) {
		color: rgba(0, 0, 0, 0.7);
	}
}
</style>
