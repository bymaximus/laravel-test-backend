<template>
	<div :class="configuracaoPagina.nomeEstilo + 'Visualizar'">
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
							name="email"
							label="Email"
							v-model="camposEditar.email"
							class="w-full campoRequerido"
							readonly
							:disabled="backendOcupado"
						/>
					</div>
				</div>
				<div class="vx-row mb-3">
					<div class="vx-col w-1/2">
						<label
							for="id_estado"
							class="vs-select--label campoRequerido"
						>Estado</label>
						<v-select
							name="id_estado"
							label="nome"
							placeholder="Estado"
							v-model="camposEditar.idEstado"
							class="w-full listagemEstado"
							:filterable="true"
							:searchable="true"
							:reduce="item => item.id"
							:options="listaEstados"
							:disabled="true"
							:loading="obtendoListas"
							readonly
						>
							<div slot="no-options">Nenhum registro disponível.</div>
						</v-select>
					</div>
					<div class="vx-col w-1/2">
						<label
							for="id_cidade"
							class="vs-select--label campoRequerido"
						>Cidade</label>
						<v-select
							name="id_cidade"
							label="nome"
							placeholder="Cidade"
							v-model="camposEditar.idCidade"
							class="w-full listagemEstado"
							:filterable="true"
							:searchable="true"
							:reduce="item => item.id"
							:options="listaCidades"
							:disabled="true"
							:loading="obtendoListas"
							readonly
						>
							<div slot="no-options">Nenhum registro disponível.</div>
						</v-select>
					</div>
				</div>
				<div class="vx-row mb-3">
					<div class="vx-col w-1/2">
						<vs-input
							name="bairro"
							label="Bairro"
							v-model="camposEditar.bairro"
							class="w-full campoRequerido"
							:disabled="backendOcupado"
							readonly
						/>
					</div>
					<div class="vx-col w-1/2">
						<vs-input
							name="rua"
							label="Rua"
							v-model="camposEditar.rua"
							class="w-full campoRequerido"
							:disabled="backendOcupado"
							readonly
						/>
					</div>
				</div>
				<div class="vx-row mb-3">
					<div class="vx-col w-1/2">
						<vs-input
							name="numero"
							label="Número"
							v-model="camposEditar.numero"
							class="w-full"
							:disabled="backendOcupado"
							readonly
						/>
					</div>
					<div class="vx-col w-1/2">
						<vs-input
							name="complemento"
							label="Complemento"
							v-model="camposEditar.complemento"
							class="w-full"
							:disabled="backendOcupado"
							readonly
						/>
					</div>
				</div>
				<div class="vx-row mb-3 mt-10">
					<div class="vx-col w-full sm:w-1/2">
						<vs-button
							color="warning"
							type="filled"
							:disabled="backendOcupado"
							@click="cancelar"
						>Voltar</vs-button>
					</div>
					<div class="vx-col w-full sm:w-1/2">
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
			listaCidades: [],
			mensagemErro: "",
			camposEditar: {
				email: "",
				idEstado: null,
				idCidade: null,
				bairro: "",
				rua: "",
				numero: "",
				complemento: "",
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
	},
	methods: {
		cancelar() {
			if (this.backendOcupado) {
				return;
			}
			this.backendOcupado = true;
			this.$router.push(this.configuracaoPagina.routerPath);
		},
		carregarRegistro(registro) {
			this.camposEditar.email = registro.email;
			this.camposEditar.idEstado = registro.id_estado;
			this.camposEditar.idCidade = registro.id_cidade;
			this.camposEditar.bairro = registro.bairro;
			this.camposEditar.rua = registro.rua;
			this.camposEditar.numero = registro.numero;
			this.camposEditar.complemento = registro.complemento;
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
				.get(this.backendUrl + `/visualizar/${this.$route.params.id}`)
				.then(response => {
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
							if (
								response.data.hasOwnProperty("cidades") &&
								response.data.cidades &&
								response.data.cidades instanceof Array
							) {
								this.listaCidades = response.data.cidades;
								if (
									response.data.hasOwnProperty("registro") &&
									response.data.registro &&
									response.data.registro instanceof Object
								) {
									this.carregarRegistro(
										Object.assign({}, response.data.registro)
									);
								} else {
									this.$vs.notify({
										title: "Erro",
										text:
											"Incapaz de carregar o " +
											this.backendModel.toLowerCase() +
											".",
										iconPack: "feather",
										icon: "icon-alert-circle",
										color: "danger",
									});
								}
							} else {
								this.$vs.notify({
									title: "Erro",
									text: "Incapaz de carregar a lista de cidades.",
									iconPack: "feather",
									icon: "icon-alert-circle",
									color: "danger",
								});
							}
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
							text:
								"Incapaz de carregar o " +
								this.backendModel.toLowerCase() +
								".",
							iconPack: "feather",
							icon: "icon-alert-circle",
							color: "danger",
						});
					}
					this.$nextTick(() => {
						this.backendOcupado = false;
						this.obtendoListas = false;
					});
				})
				.catch(error => {
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
					this.backendOcupado = false;
					this.obtendoListas = false;
				});
		}
	},
};
</script>
<style lang="scss">
@import "./configs.scss";

.pagina#{$nomePagina}Visualizar {
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
