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
						<vs-input
							name="sigla"
							label="Sigla"
							v-model="camposCriar.sigla"
							class="w-full campoRequerido"
							v-validate="'required|min:2|max:2'"
							val-icon-danger="clear"
							val-icon-success="done"
							data-vv-as="Sigla"
							:danger="errors.has('sigla')"
							:success="!errors.has('sigla') && camposCriar.sigla != ''"
							:color="!errors.has('sigla') ? 'success' : 'danger'"
							:disabled="backendOcupado"
							@keyup.enter.native="processarCriar"
						/>
						<span
							class="text-danger text-sm mt-2"
							v-show="errors.has('sigla')"
							:class="[
                                    {'block' : errors.has('sigla')}
                                ]"
						>{{ errors.first('sigla') }}</span>
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
	data() {
		return {
			configuracaoPagina: require("./configs.js"),
			backendOcupado: true,
			mensagemErro: "",
			camposCriar: {
				nome: "",
				sigla: "",
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
				sigla: this.camposCriar.sigla,
			};
		},
	},
	mounted() {
		this.backendOcupado = false;
		if (!this.$acl.check("modificar")) {
			this.cancelar();
		} else {
			this.$nextTick(() => {
				this.$refs.primeiroCampo.$el.focus();
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
