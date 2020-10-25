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
							name="email"
							label="Email"
							v-model="camposEditar.email"
							class="w-full campoRequerido"
							v-validate="'required|min:3|max:100|email'"
							val-icon-danger="clear"
							val-icon-success="done"
							data-vv-as="Email"
							:danger="errors.has('email')"
							:success="!errors.has('email') && camposEditar.email != ''"
							:color="!errors.has('email') ? 'success' : 'danger'"
							:disabled="backendOcupado"
							ref="primeiroCampo"
						/>
						<span
							class="text-danger text-sm mt-2"
							v-show="errors.has('email')"
							:class="[
                                    {'block' : errors.has('email')}
                                ]"
						>{{ errors.first('email') }}</span>
					</div>
				</div>
				<div class="vx-row mb-3">
					<div class="vx-col w-full">
						<vs-input
							name="nome"
							label="Nome"
							v-model="camposEditar.nome"
							class="w-full campoRequerido"
							v-validate="'required|min:3|max:100'"
							val-icon-danger="clear"
							val-icon-success="done"
							data-vv-as="Nome"
							:danger="errors.has('nome')"
							:success="!errors.has('nome') && camposEditar.nome != ''"
							:color="!errors.has('nome') ? 'success' : 'danger'"
							:disabled="backendOcupado"
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
						<label
							for="id_imovel"
							class="vs-select--label campoRequerido"
							:class="[
								{'input-select-label-success--active' : !errors.has('id_imovel') && camposEditar.idImovel != '' && campoIdImovelFocado},
								{'input-select-label-danger--active' : errors.has('id_imovel')}
							]"
						>Imóvel</label>
						<v-select
							name="id_imovel"
							label="endereco"
							placeholder="Imóvel"
							v-model="camposEditar.idImovel"
							class="w-full listagemImovel"
							v-validate="{ required: true, included: listaImoveis.map(s => s.id) }"
							data-vv-as="Imóvel"
							:filterable="true"
							:searchable="true"
							:reduce="item => item.id"
							:options="listaImoveis"
							:disabled="backendOcupado"
							:loading="obtendoListas"
							@search:focus="campoIdImovelFocado = true"
							@search:blur="campoIdImovelFocado = false"
						>
							<div slot="no-options">Nenhum registro disponível.</div>
						</v-select>
						<span
							class="text-danger text-sm mt-2"
							v-show="errors.has('id_imovel')"
							:class="[
								{'block' : errors.has('id_imovel')}
							]"
						>{{ errors.first('id_imovel') }}</span>
					</div>
				</div>
				<div class="vx-row mb-3">
					<div class="vx-col w-1/2">
						<label
							for="tipo_pessoa"
							class="vs-select--label campoRequerido tipoPessoaLabel"
							:class="[
								{'input-select-label-success--active' : !errors.has('tipo_pessoa') && camposEditar.tipoPessoa && camposEditar.tipoPessoa != ''},
								{'input-select-label-danger--active' : errors.has('tipo_pessoa')}
							]"
						>Tipo Pessoa</label>
						<vs-radio
							name="tipo_pessoa"
							v-model="camposEditar.tipoPessoa"
							:vs-value="1"
							v-validate="'required|included:1,2'"
							:disabled="backendOcupado"
						>Física</vs-radio>
						<vs-radio
							name="tipo_pessoa"
							v-model="camposEditar.tipoPessoa"
							:vs-value="2"
							:disabled="backendOcupado"
						>Jurídica</vs-radio>
						<span
							class="text-danger text-sm mt-2"
							v-show="errors.has('tipo_pessoa')"
							:class="[
								{'block' : errors.has('tipo_pessoa')}
							]"
						>{{ errors.first('tipo_pessoa') }}</span>
					</div>
					<div class="vx-col w-1/2">
						<vs-input
							name="documento"
							label="Documento"
							v-model="camposEditar.documento"
							class="w-full campoRequerido"
							v-validate.continues="{ required: true, validarTipoPessoaDocumento: {tipoPessoa: camposEditar.tipoPessoa, errors: this.errors} }"
							type="tel"
							v-mask="tipoPessoaMascara"
							:placeholder="tipoPessoaMascara"
							val-icon-danger="clear"
							val-icon-success="done"
							data-vv-as="Documento"
							:danger="errors.has('documento')"
							:success="!errors.has('documento') && camposEditar.documento != ''"
							:color="!errors.has('documento') ? 'success' : 'danger'"
							:disabled="backendOcupado"
						/>
						<span
							class="text-danger text-sm mt-2"
							v-show="errors.has('documento')"
							:class="[
								{'block' : errors.has('documento')}
							]"
						>{{ errors.first('documento') }}</span>
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
import Vue from "vue";
import axios from "@/axios.js";
import vSelect from "vue-select";
import VueMask from "v-mask";
import { Validator, Rules } from "vee-validate";

Vue.use(VueMask);

const validarCPF = function(cpf) {
	cpf = cpf.replace(/[^\d]+/g,'');
	if(cpf == '') return false;
	// Elimina CPFs invalidos conhecidos
	if (cpf.length != 11 ||
		cpf == "00000000000" ||
		cpf == "11111111111" ||
		cpf == "22222222222" ||
		cpf == "33333333333" ||
		cpf == "44444444444" ||
		cpf == "55555555555" ||
		cpf == "66666666666" ||
		cpf == "77777777777" ||
		cpf == "88888888888" ||
		cpf == "99999999999")
			return false;
	// Valida 1o digito
	var add = 0;
	var rev = 0;
	for (var i=0; i < 9; i ++)
		add += parseInt(cpf.charAt(i)) * (10 - i);
		rev = 11 - (add % 11);
		if (rev == 10 || rev == 11)
			rev = 0;
		if (rev != parseInt(cpf.charAt(9)))
			return false;
	// Valida 2o digito
	add = 0;
	for (var i = 0; i < 10; i ++)
		add += parseInt(cpf.charAt(i)) * (11 - i);
	rev = 11 - (add % 11);
	if (rev == 10 || rev == 11)
		rev = 0;
	if (rev != parseInt(cpf.charAt(10)))
		return false;
	return true;
};

const validarCNPJ = function(cnpj) {
  cnpj = cnpj.replace(/[^\d]+/g,'');

    if(cnpj == '') return false;

    if (cnpj.length != 14)
        return false;

    // Elimina CNPJs invalidos conhecidos
    if (cnpj == "00000000000000" ||
        cnpj == "11111111111111" ||
        cnpj == "22222222222222" ||
        cnpj == "33333333333333" ||
        cnpj == "44444444444444" ||
        cnpj == "55555555555555" ||
        cnpj == "66666666666666" ||
        cnpj == "77777777777777" ||
        cnpj == "88888888888888" ||
        cnpj == "99999999999999")
        return false;

    // Valida DVs
    var tamanho = cnpj.length - 2
    var numeros = cnpj.substring(0,tamanho);
    var digitos = cnpj.substring(tamanho);
    var soma = 0;
    var pos = tamanho - 7;
    for (var i = tamanho; i >= 1; i--) {
      soma += numeros.charAt(tamanho - i) * pos--;
      if (pos < 2)
            pos = 9;
    }
    var resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(0))
        return false;

    tamanho = tamanho + 1;
    numeros = cnpj.substring(0,tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (var i = tamanho; i >= 1; i--) {
      soma += numeros.charAt(tamanho - i) * pos--;
      if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(1))
          return false;

    return true;
};

const validarTipoPessoaDocumento = {
	getMessage(field, args, data) {
		if (data && data.hasOwnProperty("message") && data.message) {
			return "O campo " + field + " " + data.message + ".";
		}
		return "O campo " + field + " é inválido.";
	},
	validate(value, args) {
		return new Promise((resolve) => {
			if (args &&
				args.hasOwnProperty('errors') &&
				args.errors
			) {
				args.errors.remove('tipo_pessoa');
			}
			if (! args ||
				! args.hasOwnProperty('tipoPessoa') ||
				(
					args.tipoPessoa != 1 &&
					args.tipoPessoa != 2
				)
			) {
				if (args &&
					args.hasOwnProperty('errors') &&
					args.errors
				) {
					args.errors.add({
						field: 'tipo_pessoa',
						msg: 'O campo Tipo Pessoa é inválido.'
					});
				}
				resolve({
					valid: false,
					data: undefined,
				});
				return;
			}
			if (value &&
				Rules.required.validate(value)
			) {
				if (args.tipoPessoa == 1) {
					if (validarCPF(value)) {
						resolve({
							valid: true,
							data: undefined,
						});
					} else {
						resolve({
							valid: false,
							data: undefined,
						});
					}
				} else if (args.tipoPessoa == 2) {
					if (validarCNPJ(value)) {
						resolve({
							valid: true,
							data: undefined,
						});
					} else {
						resolve({
							valid: false,
							data: undefined,
						});
					}
				} else {
					if (args &&
						args.hasOwnProperty('errors') &&
						args.errors
					) {
						args.errors.add({
							field: 'tipo_pessoa',
							msg: 'O campo Tipo Pessoa é inválido.'
						});
					}
					resolve({
						valid: false,
						data: undefined,
					});
				}
			} else {
				resolve({
					valid: false,
					data: { message: "é obrigatório" },
				});
			}
		});
	},
};
Validator.extend(
	"validarTipoPessoaDocumento",
	validarTipoPessoaDocumento
);


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
			listaImoveis: [],
			campoIdImovelFocado: false,
			mensagemErro: "",
			camposEditar: {
				idImovel: null,
				nome: "",
				email: "",
				tipoPessoa: null,
				documento: "",
			},
		};
	},
	computed: {
		tipoPessoaMascara() {
			if (this.camposEditar.tipoPessoa == 1) {
				return '###.###.###-##';
			} else if (this.camposEditar.tipoPessoa == 2) {
				return '##.###.###/####-##';
			}
			return '';
		},
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
				id_imovel: this.camposEditar.idImovel,
				email: this.camposEditar.email,
				nome: this.camposEditar.nome,
				tipo_pessoa: this.camposEditar.tipoPessoa,
				documento: this.camposEditar.documento,
			};
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
			this.camposEditar.idImovel = registro.id_imovel;
			this.camposEditar.email = registro.email;
			this.camposEditar.nome = registro.nome;
			this.camposEditar.documento = registro.documento;
			this.$nextTick(() => {
				this.camposEditar.tipoPessoa = registro.tipo_pessoa;
			});
		},
		processarEditar() {
			if (this.backendOcupado) {
				return;
			}
			this.mensagemErro = "";
			this.backendOcupado = true;
			this.$validator.validate().then(result => {
				if (result) {
					axios
						.patch(
							this.backendUrl + `/${this.$route.params.id}`,
							this.camposFormularioEditar
						)
						.then(response => {
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
								this.backendOcupado = false;
							}
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
									).forEach(key => {
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
									).forEach(key => {
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
	mounted() {
		if (!this.$acl.check("modificar")) {
			this.backendOcupado = false;
			this.cancelar();
		} else {
			this.backendOcupado = true;
			this.obtendoListas = true;
			axios
				.get(this.backendUrl + `/${this.$route.params.id}`)
				.then(response => {
					if (
						response &&
						response.status == 200 &&
						response.data &&
						response.data instanceof Object
					) {
						if (
							response.data.hasOwnProperty("imoveis") &&
							response.data.imoveis &&
							response.data.imoveis instanceof Array
						) {
							this.listaImoveis = response.data.imoveis;
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
								text: "Incapaz de carregar a lista de imóveis.",
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
						this.$refs.primeiroCampo.$el.focus();
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

.pagina#{$nomePagina}Editar {
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
	div.listagemImovel ul.vs__dropdown-menu {
		max-height: 200px;
	}
	label.tipoPessoaLabel {
		clear: both;
		display: block;
		margin-bottom: 12px;
	}
}
</style>
