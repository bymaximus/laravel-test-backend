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
							name="nome"
							label="Nome"
							v-model="camposCriar.nome"
							class="w-full campoRequerido"
							v-validate="'required|min:1|max:100'"
							val-icon-danger="clear"
							val-icon-success="done"
							data-vv-as="Nome"
							:danger="errors.has('nome')"
							:success="!errors.has('nome') && camposCriar.nome != ''"
							:color="!errors.has('nome') ? 'success' : 'danger'"
							:disabled="backendOcupado"
							ref="primeiroCampo"
						/>
						<span
							class="text-danger text-sm mt-2"
							v-show="errors.has('nome')"
							:class="[
                                    {'block' : errors.has('nome')}
                                ]"
						>{{ errors.first('nome') }}</span>
					</div>
				</div>
				<div class="vx-row">
					<div class="vx-col w-full">
						<label
							for="id_estado"
							class="vs-select--label campoRequerido"
							:class="[
								{'input-select-label-success--active' : !errors.has('id_estado') && camposCriar.idEstado && camposCriar.idEstado != '' && campoIdEstadoFocado},
								{'input-select-label-danger--active' : errors.has('id_estado')}
							]"
						>Estado</label>
						<v-select
							name="id_estado"
							label="nome"
							placeholder="Estado"
							v-model="camposCriar.idEstado"
							class="w-full listagemEstado"
							v-validate="{ required: true, included: listaEstados.map(s => s.id) }"
							data-vv-as="Estado"
							:filterable="true"
							:searchable="true"
							:reduce="item => item.id"
							:options="listaEstados"
							:disabled="backendOcupado"
							:loading="obtendoListas"
							@search:focus="campoIdEstadoFocado = true"
							@search:blur="campoIdEstadoFocado = false"
						>
							<div slot="no-options">Nenhum registro disponível.</div>
						</v-select>
						<span
							class="text-danger text-sm mt-2"
							v-show="errors.has('id_estado')"
							:class="[
								{'block' : errors.has('id_estado')}
							]"
						>{{ errors.first('id_estado') }}</span>
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
	components: {
		"v-select": vSelect
	},
	props: {
		abaAtiva: {
			type: Number,
			default: null,
		},
	},
	data() {
		return {
			configuracaoPagina: require("./configs.js"),
			backendOcupado: true,
			obtendoListas: false,
			listaEstados: [],
			campoIdEstadoFocado: false,
			mensagemErro: "",
			camposCriar: {
				nome: "",
				idEstado: null,
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
				nome: this.camposCriar.nome,
				id_estado: this.camposCriar.idEstado,
			};
		},
	},
	mounted() {
		this.backendOcupado = false;
		if (!this.$acl.check("modificar")) {
			this.cancelar();
		} else {
			this.backendOcupado = true;
			this.obtendoListas = true;
			axios
				.get(this.backendUrl + "/listas")
				.then((response) => {
					if (
						response &&
						response.status == 200 &&
						response.data &&
						response.data instanceof Object
					) {
						if (
							response.data.hasOwnProperty("estados") &&
							response.data.estados &&
							response.data.estados instanceof Array
						) {
							this.listaEstados = response.data.estados;
						} else {
							this.$vs.notify({
								title: "Erro",
								text: "Incapaz de carregar a lista de estados.",
								iconPack: "feather",
								icon: "icon-alert-circle",
								color: "danger",
							});
						}
					} else {
						this.$vs.notify({
							title: "Erro",
							text: "Incapaz de carregar a lista de opções.",
							iconPack: "feather",
							icon: "icon-alert-circle",
							color: "danger",
						});
					}
					this.obtendoListas = false;
					this.backendOcupado = false;
					this.$nextTick(() => {
						setTimeout(() => {
							window
								.jQuery(this.$refs.primeiroCampo.$refs.vsinput)
								.trigger("focus");
						}, 150);
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
									text: `${this.backendModel} criada.`,
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
	div.listagemEstado ul.vs__dropdown-menu {
		max-height: 200px;
	}
}
</style>
