<template>
	<div :class="configuracaoPagina.nomeEstilo + 'RemovidoHistoricoVisualizar'">
		<div class="vx-card p-6">
			<vs-alert
				:active="(mensagemErro && mensagemErro != '' ? true : false)"
				color="danger"
				class="mb-10"
			>
				<span v-html="mensagemErro"></span>
			</vs-alert>
			<div class="vx-row mb-3">
				<div class="vx-col w-1/2 pl-8 flex items-center">
					<p class="font-semibold">Valores Criados/Removidos/Alterados</p>
				</div>
				<div class="vx-col w-1/2 flex justify-end">
					<vs-button
						color="warning"
						type="filled"
						:disabled="backendOcupado"
						@click="cancelar"
					>Voltar</vs-button>
				</div>
			</div>
			<div class="vx-row mb-3">
				<div class="vx-col w-full">
					<div
						class="historicoDiferencas"
						v-html="diferencaVisual"
					></div>
				</div>
			</div>
			<div class="vx-row mb-3 mt-10">
				<div class="vx-col w-full pl-8">
					<h6>Legenda</h6>
				</div>
			</div>
			<div class="vx-row">
				<div class="vx-col w-full">
					<div class="vx-row">
						<div class="vx-col w-48">
							<div class="jsondiffpatch-delta jsondiffpatch-node jsondiffpatch-child-node-type-object">
								<ul class="jsondiffpatch-node jsondiffpatch-node-type-array">
									<li class="jsondiffpatch-modified">
										<div class="jsondiffpatch-value jsondiffpatch-left-value">
											<pre>"Valor Antigo"</pre>
										</div>
									</li>
								</ul>
							</div>
						</div>
						<div
							class="vx-col w-auto"
							style="align-self: center;"
						>
							<span>Valor antigo removido/alterado.</span>
						</div>
					</div>
				</div>
			</div>
			<div class="vx-row">
				<div class="vx-col w-full">
					<div class="vx-row">
						<div class="vx-col w-48">
							<div class="jsondiffpatch-delta jsondiffpatch-node jsondiffpatch-child-node-type-object">
								<ul class="jsondiffpatch-node jsondiffpatch-node-type-array">
									<li class="jsondiffpatch-modified">
										<div class="jsondiffpatch-value jsondiffpatch-right-value">
											<pre>"Valor Novo"</pre>
										</div>
									</li>
								</ul>
							</div>
						</div>
						<div
							class="vx-col w-auto"
							style="align-self: center;"
						>
							<span>Valor novo adicionado/alterado.</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import axios from "@/axios.js";

import "jsondiffpatch/dist/formatters-styles/html.css";
import "jsondiffpatch/dist/formatters-styles/annotated.css";

var jsonDiffPatch = require("jsondiffpatch");
import * as jsonDiffPatchHtmlCustom from "@/components/jsondiffpatch/HtmlFormatterCustom.js";

var camposDiferancas = jsonDiffPatch.create({
	// used to match objects when diffing arrays, by default only === operator is used
	objectHash: function (obj) {
		// this function is used only to when objects are not equal by ref
		return obj._id || obj.id;
	},
	arrays: {
		// default true, detect items moved inside the array (otherwise they will be registered as remove+add)
		detectMove: false,
		// default false, the value of items moved is not included in deltas
		includeValueOnMove: false,
	},
	textDiff: {
		// default 60, minimum string length (left and right sides) to use text diff algorythm: google-diff-match-patch
		minLength: 60,
	},
	propertyFilter: function (name, context) {
		/*
       this optional function can be specified to ignore object properties (eg. volatile data)
        name: property name, present in either context.left or context.right objects
        context: the diff context (has context.left and context.right objects)
      */
		return name.slice(0, 1) !== "$";
	},
	cloneDiffValues: false /* default false. if true, values in the obtained delta will be cloned
      (using jsondiffpatch.clone by default), to ensure delta keeps no references to left or right objects. this becomes useful if you're diffing and patching the same objects multiple times without serializing deltas.
      instead of true, a function can be specified here to provide a custom clone(value)
      */,
});

export default {
	props: {
		abaAtiva: {
			type: Number,
			default: null,
		},
	},
	data() {
		return {
			configuracaoPagina: require("./../configs.js"),
			backendOcupado: true,
			mensagemErro: "",
			camposEditar: {
				valoresNovos: null,
				valoresAntigos: null,
			},
		};
	},
	computed: {
		backendUrl() {
			return (
				this.$urlBasePath +
				this.configuracaoPagina.backendUrl +
				"/removido" +
				`/${this.$route.params.id}` +
				"/historico"
			);
		},
		backendModel() {
			return this.configuracaoPagina.backendModel;
		},
		diferencaDelta() {
			if (
				this.camposEditar.valoresNovos !== null &&
				this.camposEditar.valoresAntigos !== null
			) {
				return camposDiferancas.diff(
					this.camposEditar.valoresAntigos,
					this.camposEditar.valoresNovos
				);
			}
			return null;
		},
		diferencaVisual() {
			if (
				this.camposEditar.valoresNovos !== null &&
				this.camposEditar.valoresAntigos !== null &&
				this.diferencaDelta !== null
			) {
				return jsonDiffPatchHtmlCustom.format(
					this.diferencaDelta,
					this.camposEditar.valoresAntigos
				);
			}
			return null;
		},
	},
	methods: {
		cancelar() {
			if (this.backendOcupado) {
				return;
			}
			this.backendOcupado = true;
			this.$router.push({
				name:
					this.configuracaoPagina.routerName + "-removido-historico",
				params: {
					id: this.$route.params.id,
				},
			});
		},
		carregarRegistro(registro) {
			this.camposEditar.valoresNovos = registro.valores_novos;
			this.camposEditar.valoresAntigos = registro.valores_antigos;
		},
	},
	mounted() {
		this.backendOcupado = true;
		axios.get(this.backendUrl + `/${this.$route.params.idHistorico}`)
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
					} else {
						this.$vs.notify({
							title: "Erro",
							text:
								"Incapaz de carregar o histórico de " +
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
						text:
							"Incapaz de carregar o histórico de " +
							this.backendModel.toLowerCase() +
							".",
						iconPack: "feather",
						icon: "icon-alert-circle",
						color: "danger",
					});
				}
				this.backendOcupado = false;
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
				if (error && error.response && error.response.status == 401) {
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
	},
};
</script>
<style lang="scss">
@import "./../configs.scss";

.pagina#{$nomePagina}RemovidoHistoricoVisualizar {
	div.historicoDiferencas {
		width: 100%;
	}

	div.historicoDiferencas div.jsondiffpatch-delta {
		width: 100%;
	}
	div.historicoDiferencas div.jsondiffpatch-delta ul {
		li.jsondiffpatch-unchanged:nth-of-type(even),
		li.jsondiffpatch-modified:nth-of-type(even) {
			background-color: #f8f8f8 !important;
		}
	}
}
</style>
