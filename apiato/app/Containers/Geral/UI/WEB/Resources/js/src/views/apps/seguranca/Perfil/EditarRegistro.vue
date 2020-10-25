<template>
	<div :class="configuracaoPagina.nomeEstilo + 'Editar'">
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
							v-model="camposEditar.nome"
							class="w-full campoRequerido"
							v-validate="'required|min:1|max:100'"
							val-icon-danger="clear"
							val-icon-success="done"
							data-vv-as="Nome"
							:danger="errors.has('nome')"
							:success="!errors.has('nome') && camposEditar.nome != ''"
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
				<div class="vx-row mb-3">
					<div class="vx-col w-full">
						<h6>Histórico de Alterações</h6>
					</div>
				</div>
				<div class="vx-row">
					<div class="vx-col w-full flex">
						<vs-checkbox
							v-model="camposEditar.historicoAlteracao"
							:disabled="backendOcupado"
						>Acesso a alterações anteriores e registros removidos?</vs-checkbox>
					</div>
				</div>
				<div class="vx-row mt-5">
					<div class="vx-col w-full">
						<p class="font-semibold">Funcionalidades Permitidas</p>
					</div>
				</div>
				<div class="vx-row mb-12 mt-3">
					<div class="vx-col w-full">
						<v-jstree
							:disabled="backendOcupado"
							:data="funcionalidades"
							@item-click="onFuncionalidadeClick"
							ref="listaFuncionalidades"
							loading-text="Carregando..."
							text-field-name="nome"
							value-field-name="codigo"
							:show-checkbox="true"
							:multiple="true"
							:allow-batch="true"
							:draggable="false"
							:expand-timer="true"
							:execute-sibling-movement="false"
						>
							<template slot-scope="_">
								<div style="display: inherit;">
									<i
										:class="_.vm.themeIconClasses"
										role="presentation"
										v-if="!_.model.loading"
									></i>
									{{_.model.nome}}
								</div>
							</template>
						</v-jstree>
					</div>
				</div>
				<div class="vx-row mb-3">
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
							:disabled="backendOcupado || formularioEditarInvalido"
							@click="processarEditar"
						>Salvar {{ backendModel }}</vs-button>
					</div>
				</div>
			</form>
		</div>
	</div>
</template>

<script>
import axios from "@/axios.js";
import VJstree from "vue-jstree-extended";

export default {
	$_veeValidate: {
		validator: "new",
	},
	components: {
		VJstree,
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
			funcionalidades: [],
			funcionalidadesStatus: {},
			camposEditar: {
				nome: "",
				historicoAlteracao: false,
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
		formularioEditarInvalido() {
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
		camposFormularioEditar() {
			return {
				nome: this.camposEditar.nome,
				historico_alteracao: this.camposEditar.historicoAlteracao,
			};
		},
	},
	methods: {
		funcionalidadeSelecionarParentes(node) {
			if (
				node &&
				node.data &&
				node.data.hasOwnProperty("selected") &&
				node.data.hasOwnProperty("codigo") &&
				node.data.codigo != -1
			) {
				if (!node.data.selected) {
					node.data.selected = true;
				}
				if (
					node.$options &&
					node.$options.parent &&
					node.$options.parent.data &&
					node.$options.parent.data.hasOwnProperty("selected") &&
					node.$options.parent.data.hasOwnProperty("codigo") &&
					node.$options.parent.data.codigo != -1
				) {
					this.funcionalidadeSelecionarParentes(node.$options.parent);
				}
			}
		},
		funcionalidadeDeselecionarParentes(node) {
			if (
				node &&
				node.data &&
				node.data.hasOwnProperty("selected") &&
				node.data.hasOwnProperty("codigo") &&
				node.data.hasOwnProperty("children") &&
				node.data.codigo != -1 &&
				node.data.children &&
				node.data.children.length > 1
			) {
				if (node.data.selected) {
					var selected = node.data.children.findIndex(
						(t) => t.selected === true
					);
					if (selected === -1) {
						node.data.selected = false;
					}
				}
				if (
					node.$options &&
					node.$options.parent &&
					node.$options.parent.data &&
					node.$options.parent.data.hasOwnProperty("selected") &&
					node.$options.parent.data.hasOwnProperty("codigo") &&
					node.$options.parent.data.codigo != -1
				) {
					this.funcionalidadeDeselecionarParentes(
						node.$options.parent
					);
				}
			}
		},
		funcionalidadeFilhos(filhos) {
			var items = {};
			if (filhos && filhos.length > 0) {
				for (var key in filhos) {
					if (
						filhos[key] &&
						filhos[key].hasOwnProperty("codigo") &&
						filhos[key].hasOwnProperty("selected") &&
						filhos[key].codigo > 0
					) {
						items[filhos[key].codigo] = {
							ativo: filhos[key].selected,
							sub: {},
						};
						if (
							filhos[key].hasOwnProperty("children") &&
							filhos[key].children &&
							filhos[key].children.length > 0
						) {
							items[
								filhos[key].codigo
							].sub = this.funcionalidadeFilhos(
								filhos[key].children
							);
						}
					}
				}
			}
			return items;
		},
		desabilitarFuncionalidadeFilhos(filhos) {
			var items = {};
			if (filhos && filhos.length > 0) {
				for (var key in filhos) {
					if (
						filhos[key] &&
						filhos[key].hasOwnProperty("id") &&
						filhos[key].hasOwnProperty("disabled")
					) {
						items[filhos[key].id] = {
							disabled: filhos[key].disabled,
							sub: {},
						};
						filhos[key].disabled = true;
						if (
							filhos[key].hasOwnProperty("children") &&
							filhos[key].children &&
							filhos[key].children.length > 0
						) {
							items[
								filhos[key].id
							].sub = this.desabilitarFuncionalidadeFilhos(
								filhos[key].children
							);
						}
					}
				}
			}
			return items;
		},
		desabilitarFuncionalidades() {
			this.funcionalidadesStatus = {};
			if (this.funcionalidades) {
				for (var key in this.funcionalidades) {
					if (
						this.funcionalidades[key] &&
						this.funcionalidades[key].hasOwnProperty("id") &&
						this.funcionalidades[key].hasOwnProperty("disabled")
					) {
						this.funcionalidadesStatus[
							this.funcionalidades[key].id
						] = {
							disabled: this.funcionalidades[key].disabled,
							sub: {},
						};
						this.funcionalidades[key].disabled = true;
						if (
							this.funcionalidades[key].hasOwnProperty(
								"children"
							) &&
							this.funcionalidades[key].children &&
							this.funcionalidades[key].children.length > 0
						) {
							this.funcionalidadesStatus[
								this.funcionalidades[key].id
							].sub = this.desabilitarFuncionalidadeFilhos(
								this.funcionalidades[key].children
							);
						}
					}
				}
			}
		},
		restaurarFuncionalidadeFilhos(filhosClone, filhos) {
			if (filhos && filhos.length > 0 && filhosClone) {
				for (var key in filhos) {
					if (
						filhos[key] &&
						filhos[key].hasOwnProperty("id") &&
						filhos[key].hasOwnProperty("disabled") &&
						filhosClone.hasOwnProperty(filhos[key].id) &&
						filhosClone[filhos[key].id]
					) {
						filhos[key].disabled =
							filhosClone[filhos[key].id].disabled;
						if (
							filhos[key].hasOwnProperty("children") &&
							filhos[key].children &&
							filhos[key].children.length > 0 &&
							filhosClone[filhos[key].id].hasOwnProperty("sub") &&
							filhosClone[filhos[key].id].sub
						) {
							this.restaurarFuncionalidadeFilhos(
								filhosClone[filhos[key].id].sub,
								filhos[key].children
							);
						}
					}
				}
			}
		},
		restaurarFuncionalidades() {
			if (this.funcionalidades && this.funcionalidadesStatus) {
				for (var key in this.funcionalidades) {
					if (
						this.funcionalidades[key] &&
						this.funcionalidades[key].hasOwnProperty("id") &&
						this.funcionalidades[key].hasOwnProperty("disabled") &&
						this.funcionalidadesStatus.hasOwnProperty(
							this.funcionalidades[key].id
						) &&
						this.funcionalidadesStatus[this.funcionalidades[key].id]
					) {
						this.funcionalidades[
							key
						].disabled = this.funcionalidadesStatus[
							this.funcionalidades[key].id
						].disabled;
						if (
							this.funcionalidades[key].hasOwnProperty(
								"children"
							) &&
							this.funcionalidades[key].children &&
							this.funcionalidades[key].children.length > 0 &&
							this.funcionalidadesStatus[
								this.funcionalidades[key].id
							].hasOwnProperty("sub") &&
							this.funcionalidadesStatus[
								this.funcionalidades[key].id
							].sub
						) {
							this.restaurarFuncionalidadeFilhos(
								this.funcionalidadesStatus[
									this.funcionalidades[key].id
								].sub,
								this.funcionalidades[key].children
							);
						}
					}
				}
			}
			this.funcionalidadesStatus = {};
		},
		onFuncionalidadeClick(node, item) {
			if (this.backendOcupado) {
				return;
			}
			if (
				item &&
				item.hasOwnProperty("selected") &&
				node &&
				node.$options &&
				node.$options.parent &&
				node.$options.parent.data &&
				node.$options.parent.data.hasOwnProperty("selected") &&
				node.$options.parent.data.hasOwnProperty("codigo") &&
				node.$options.parent.data.codigo != -1
			) {
				if (item.selected) {
					this.funcionalidadeSelecionarParentes(node.$options.parent);
				} else {
					this.funcionalidadeDeselecionarParentes(
						node.$options.parent
					);
				}
			}
		},
		cancelar() {
			if (this.backendOcupado) {
				return;
			}
			this.backendOcupado = true;
			this.$router.push(this.configuracaoPagina.routerPath);
		},
		carregarRegistro(registro) {
			this.camposEditar.nome = registro.nome;
			this.camposEditar.historicoAlteracao = registro.historico_alteracao;
		},
		processarEditar() {
			if (this.backendOcupado) {
				return;
			}
			this.mensagemErro = "";
			this.backendOcupado = true;
			this.desabilitarFuncionalidades();
			this.$validator.validate().then((result) => {
				if (result) {
					var camposFormularioEditarClone = this.lodash.cloneDeep(
						this.camposFormularioEditar
					);
					camposFormularioEditarClone.funcionalidades = {};
					for (var key in this.funcionalidades) {
						if (
							this.funcionalidades[key] &&
							this.funcionalidades[key].hasOwnProperty(
								"codigo"
							) &&
							this.funcionalidades[key].hasOwnProperty("selected")
						) {
							if (this.funcionalidades[key].codigo > 0) {
								camposFormularioEditarClone.funcionalidades[
									this.funcionalidades[key].codigo
								] = {
									ativo: this.funcionalidades[key].selected,
									sub: {},
								};
								if (
									this.funcionalidades[key].hasOwnProperty(
										"children"
									) &&
									this.funcionalidades[key].children &&
									this.funcionalidades[key].children.length >
										0
								) {
									camposFormularioEditarClone.funcionalidades[
										this.funcionalidades[key].codigo
									].sub = this.funcionalidadeFilhos(
										this.funcionalidades[key].children
									);
								}
							} else if (
								this.funcionalidades[key].hasOwnProperty(
									"children"
								) &&
								this.funcionalidades[key].children &&
								this.funcionalidades[key].children.length > 0
							) {
								camposFormularioEditarClone.funcionalidades = this.funcionalidadeFilhos(
									this.funcionalidades[key].children
								);
							}
						}
					}
					axios
						.patch(
							this.backendUrl + `/${this.$route.params.id}`,
							camposFormularioEditarClone
						)
						.then((response) => {
							if (
								response &&
								response.status == 200 &&
								response.data &&
								response.data.status == "atualizado"
							) {
								this.$vs.notify({
									title: "Sucesso",
									text: `${this.backendModel} atualizado.`,
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
										"Dados de resposta do registro atualizado inválidos.",
									iconPack: "feather",
									icon: "icon-alert-circle",
									color: "danger",
								});
								this.restaurarFuncionalidades();
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
										this.camposFormularioEditar
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
							this.restaurarFuncionalidades();
							this.backendOcupado = false;
						});
				} else {
					this.restaurarFuncionalidades();
					this.backendOcupado = false;
				}
			});
		},
	},
	mounted() {
		if (!this.$acl.check("modificar")) {
			this.backendOcupado = false;
			this.cancelar();
		} else {
			this.backendOcupado = true;
			this.funcionalidades = [
				this.$refs.listaFuncionalidades.initializeLoading(),
			];
			this.$refs.listaFuncionalidades.handleAsyncLoad(
				this.funcionalidades,
				this.$refs.listaFuncionalidades
			);
			axios
				.get(this.backendUrl + `/${this.$route.params.id}`)
				.then((response) => {
					if (
						response &&
						response.status == 200 &&
						response.data &&
						response.data instanceof Object
					) {
						if (
							response.data.hasOwnProperty("registro") &&
							response.data.registro &&
							response.data.registro instanceof Object
						) {
							this.carregarRegistro(
								Object.assign({}, response.data.registro)
							);
							if (
								response.data.hasOwnProperty(
									"funcionalidades"
								) &&
								response.data.funcionalidades &&
								response.data.funcionalidades instanceof Array
							) {
								this.funcionalidades =
									response.data.funcionalidades;
								this.$refs.listaFuncionalidades.initializeData(
									this.funcionalidades
								);
							} else {
								this.$vs.notify({
									title: "Erro",
									text:
										"Incapaz de carregar a lista de funcionalidades do " +
										this.backendModel.toLowerCase() +
										".",
									iconPack: "feather",
									icon: "icon-alert-circle",
									color: "danger",
								});
								this.funcionalidades = [];
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
							this.funcionalidades = [];
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
						this.funcionalidades = [];
					}
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
					this.backendOcupado = false;
				});
		}
	},
};
</script>
<style lang="scss">
@import "./configs.scss";

.pagina#{$nomePagina}Editar {
	div.tree svg.svg-inline--fa.tree-icon {
		width: 20px;
		height: 20px;
		margin-right: 5px;
	}
	div.tree i.tree-icon.icon-labexpress {
		background-image: url(/images/logo/labexpress.png);
		background-size: 24px 20px;
		background-position-y: 2px;
		background-position-x: 1px;
	}
	div.tree li[role="treeitem"].tree-node {
		background-color: inherit !important;
	}
	div.tree div.tree-anchor.tree-selected {
		background-color: inherit !important;
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
