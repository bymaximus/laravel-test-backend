<template>
	<div class="vx-card p-6">
		<vs-prompt
			:class="$parent.configuracaoPagina.nomeEstilo + 'Dialogo editar-dialogo'"
			:title="'Remover ' + backendModel"
			accept-text="Remover"
			cancel-text="Cancelar"
			button-accept="filled"
			button-cancel="flat"
			type="confirm"
			color="danger"
			@accept="processarRemoverRegistro"
			@cancel="cancelarRemoverRegistro"
			@close="fecharRemoverRegistro"
			@keyup.esc.native="fecharRemoverRegistro(true)"
			:is-valid="! $parent.backendOcupado && ! provedorListaRegistros.busy"
			:active.sync="removerRegistroDialogoStatus"
			ref="removerRegistroDialogo"
		>
			<VuePerfectScrollbar
				class="scroll-area p-4"
				:settings="scrollSettings"
				:key="$vs.rtl"
			>
				Tem certeza que deseja remover essa {{ backendModel.toLowerCase() }}?
				<vs-alert
					:active="(mensagemErro && mensagemErro != '' ? true : false)"
					color="danger"
					class="mb-10"
				>
					<span v-html="mensagemErro"></span>
				</vs-alert>
			</VuePerfectScrollbar>
		</vs-prompt>
		<vs-row class="mb-2">
			<vs-col
				vs-type="flex"
				vs-align="flex-start"
				vs-justify="flex-start"
				vs-w="4"
			>
				<v-select
					name="id_estado"
					label="nome"
					placeholder="Filtrar por estado"
					v-model="provedorListaRegistros.state.searchFields['id_estado']['value']"
					class="w-full listagemEstado"
					:filterable="true"
					:searchable="true"
					:reduce="item => item.id"
					:options="listaEstado"
					:disabled="provedorListaRegistros.busy || $parent.backendOcupado"
					:loading="obtendoListas"
				>
					<div slot="no-options">Nenhum registro disponível.</div>
				</v-select>
			</vs-col>
			<vs-col
				vs-type="flex"
				vs-align="flex-end"
				vs-justify="flex-end"
				vs-w="8"
			>
				<vx-input-group vs-justify="flex-end">
					<vs-input
						icon-pack="feather"
						icon="icon-search"
						placeholder="Procurar"
						v-model="provedorListaRegistrosFiltro"
						ref="provedorListaRegistrosFiltroGlobalCampo"
						:disabled="provedorListaRegistros.busy || $parent.backendOcupado"
					/>
					<template slot="append">
						<div class="append-text btn-addon">
							<vs-button
								:disabled="provedorListaRegistros.busy || $parent.backendOcupado || !provedorListaRegistrosFiltro || !provedorListaRegistrosFiltro.toString().length"
								@click="provedorListaRegistrosFiltro=''"
								color="danger"
								type="filled"
								icon-pack="feather"
								icon="icon-trash"
							></vs-button>
						</div>
					</template>
				</vx-input-group>
				<vs-button
					color="primary"
					type="filled"
					icon-pack="feather"
					icon="icon-plus"
					class="ml-2"
					v-show="$acl.check('modificar')"
					:disabled="provedorListaRegistros.busy || $parent.backendOcupado"
					@click="dialogoCriarRegistroAbrir"
				>Criar {{ $parent.backendModel }}</vs-button>
				<vx-tooltip
					color="warning"
					position="left"
					text="Histórico de Removidos"
					class="ml-2"
					v-show="$acl.check('historicoAlteracao')"
					:class="[
						{'disabled' : (provedorListaRegistros.busy || $parent.backendOcupado)},
					]"
				>
					<vs-button
						color="warning"
						type="filled"
						icon-pack="fa"
						icon="fa-history"
						v-show="$acl.check('historicoAlteracao')"
						:disabled="provedorListaRegistros.busy || $parent.backendOcupado"
						@click="historicoRegistroRemovido()"
					></vs-button>
				</vx-tooltip>
			</vs-col>
		</vs-row>
		<div class="bootstrap-inside">
			<b-table
				show-empty
				empty-text="Nenhum registro encontrado."
				empty-filtered-text="Nenhum registro encontrado relacionado a pesquisa."
				hover
				small
				striped
				responsive
				:items="provedorListaRegistros.items"
				:fields="camposListaRegistros"
				:busy="provedorListaRegistros.busy"
				:current-page="provedorListaRegistros.state.currentPage"
				:per-page="provedorListaRegistros.state.perPage"
				:filter="provedorListaRegistros.state.filter"
				:filter-ignored-fields="provedorListaRegistros.state.filterIgnoredFields"
				:filter-included-fields="provedorListaRegistros.state.filterIncludedFields"
				@refreshed="onListaRegistrosAtualizado"
				id="listaRegistros"
				ref="listaRegistros"
				:api-url="backendUrl"
			>
				<template v-slot:table-busy>
					<div class="contained-example-container">
						<div
							id="div-with-loading"
							class="vs-con-loading__container text-center text-danger"
						>
							<div class="con-vs-loading vs-loading-background-undefined vs-loading-color-undefined">
								<div
									class="vs-loading default"
									style="transform: scale(0.6);"
								>
									<div class="effect-1 effects"></div>
									<div class="effect-2 effects"></div>
									<div class="effect-3 effects"></div>
									<img src="">
								</div>
							</div>
						</div>
					</div>
				</template>
				<template v-slot:cell(dt_criacao)="row">
					<span class="text-nowrap">{{ row.value }}</span>
				</template>
				<template v-slot:cell(dt_alteracao)="row">
					<span class="text-nowrap">{{ row.value }}</span>
				</template>
				<template v-slot:cell(estado.sigla)="row">
					<vx-tooltip
						color="warning"
						position="right"
						:text="(row.item && row.item.estado && row.item.estado.nome ? row.item.estado.nome : '')"
						:class="[
							{'disabled' : (provedorListaRegistros.busy || $parent.backendOcupado)},
						]"
					>
						<span class="text-nowrap">{{ row.value }}</span>
					</vx-tooltip>
					<vx-tooltip
						color="danger"
						position="right"
						text="Estado removido"
						v-if="(row.item && row.item.estado && row.item.estado.removido === true ? true : false)"
						:class="[
							{'disabled' : (provedorListaRegistros.busy || $parent.backendOcupado)},
						]"
					>
						<vs-button
							color="danger"
							text-color="danger"
							size="small"
							type="flat"
							icon-pack="fa"
							icon="fa-exclamation-triangle"
							class="dataTablesButton registroRemovido"
							:disabled="true"
						></vs-button>
					</vx-tooltip>
				</template>
				<template v-slot:cell(nome)="row">
					<span class="text-break">{{ row.value }}</span>
				</template>
				<template v-slot:cell(acoes)="row">
					<span class="text-nowrap">
						<vx-tooltip
							color="success"
							position="left"
							text="Alterar"
							v-show="$acl.check('modificar')"
							:class="[
							{'disabled' : (provedorListaRegistros.busy || $parent.backendOcupado)},
						]"
						>
							<vs-button
								color="success"
								text-color="success"
								size="small"
								type="flat"
								icon-pack="feather"
								icon="icon-edit"
								class="dataTablesButton"
								v-show="$acl.check('modificar')"
								@click="editarRegistro(row.item, row.index, $event.target)"
								:disabled="provedorListaRegistros.busy || $parent.backendOcupado"
							></vs-button>
						</vx-tooltip>
						<vx-tooltip
							color="warning"
							position="top"
							text="Histórico de Alterações"
							v-show="$acl.check('historicoAlteracao')"
							:class="[
							{'disabled' : (provedorListaRegistros.busy || $parent.backendOcupado)},
						]"
						>
							<vs-button
								color="warning"
								text-color="warning"
								size="small"
								type="flat"
								icon-pack="fa"
								icon="fa-history"
								class="dataTablesButton"
								v-show="$acl.check('historicoAlteracao')"
								@click="historicoRegistro(row.item, row.index, $event.target)"
								:disabled="provedorListaRegistros.busy || $parent.backendOcupado || removerRegistroDialogoStatus"
							></vs-button>
						</vx-tooltip>
						<vx-tooltip
							color="danger"
							position="right"
							text="Remover"
							v-show="$acl.check('modificar')"
							:class="[
							{'disabled' : (provedorListaRegistros.busy || $parent.backendOcupado)},
						]"
						>
							<vs-button
								color="danger"
								text-color="danger"
								size="small"
								type="flat"
								icon-pack="feather"
								icon="icon-trash"
								v-show="$acl.check('modificar')"
								class="dataTablesButton"
								@click="removerRegistro(row.item, row.index, $event.target)"
								:disabled="provedorListaRegistros.busy || $parent.backendOcupado || removerRegistroDialogoStatus"
							></vs-button>
						</vx-tooltip>
					</span>
				</template>
			</b-table>
		</div>
		<vs-row>
			<vs-col
				vs-type="flex"
				vs-align="flex-start"
				vs-w="6"
			>
				<vs-row>
					<vs-col
						vs-type="flex"
						vs-align="flex-start"
						vs-w="12"
					>
						<label class="m-auto mr-2 ml-0">Paginação</label>
						<vs-dropdown
							ref='dropdown'
							vs-trigger-click
							:disabled="provedorListaRegistros.busy || $parent.backendOcupado"
							:class="[
								{'cursor-pointer' : (! provedorListaRegistros.busy && ! $parent.backendOcupado)},
								{'cursor-not-allowed' : (provedorListaRegistros.busy || $parent.backendOcupado)},
								{'disabled' : (provedorListaRegistros.busy || $parent.backendOcupado)},
							]"
						>
							<div class="p-3 border border-solid d-theme-border-grey-light rounded-lg d-theme-dark-bg cursor-pointer flex items-center justify-between font-medium">
								<span class="mr-2">{{ provedorListaRegistros.startRow  > provedorListaRegistros.endRow ? provedorListaRegistros.endRow : provedorListaRegistros.startRow }} - {{ provedorListaRegistros.endRow }} de {{ provedorListaRegistros.totalRows }}</span>
								<feather-icon
									icon="ChevronDownIcon"
									svgClasses="h-4 w-4"
								/>
							</div>
							<vs-dropdown-menu class="con-vs-dropdown--menu vs-dropdown-menu">
								<vs-dropdown-item
									:key="index"
									v-for="(option, index) in provedorListaRegistros.pageLengths"
									:disabled="provedorListaRegistros.busy || $parent.backendOcupado"
									@click="$refs.dropdown.$el.click(); provedorListaRegistros.state.perPage = option.value;"
								>
									<span>{{ (option.text == 'All' ? 'Todos' : option.text) }}</span>
								</vs-dropdown-item>
							</vs-dropdown-menu>
						</vs-dropdown>
					</vs-col>
				</vs-row>
			</vs-col>
			<vs-col
				vs-type="flex"
				vs-align="flex-end"
				vs-w="6"
			>
				<vs-pagination
					v-model="provedorListaRegistros.state.currentPage"
					class="listaRegistrosPaginacao"
					:class="[
                            {'disabled' : provedorListaRegistros.busy || $parent.backendOcupado}
                        ]"
					:total="listaRegistrosPaginaTotal"
					:disabled="provedorListaRegistros.busy || $parent.backendOcupado"
				/>
			</vs-col>
		</vs-row>
	</div>
</template>

<script>
import axios from "@/axios.js";

import ItemsProvider from "bvtnet-items-provider";
import vSelect from "vue-select";
import VuePerfectScrollbar from "vue-perfect-scrollbar";

export default {
	components: {
		"v-select": vSelect,
		VuePerfectScrollbar,
	},
	props: {
		dialogoCriarRegistroAbrir: {
			type: Function,
			required: true,
		},
		dialogoEditarRegistroAbrir: {
			type: Function,
			required: true,
		},
		dialogoHistoricoRegistroAbrir: {
			type: Function,
			required: true,
		},
		dialogoHistoricoRegistroRemovidoAbrir: {
			type: Function,
			required: true,
		},
		backendUrl: {
			type: String,
			required: true,
		},
		backendModel: {
			type: String,
			required: true,
		},
	},
	data() {
		const vm = this;
		const fields = [
			{
				key: "acoes",
				label: "Ações",
				sortable: false,
				searchable: false,
				class: "text-center",
			},
			{
				key: "estado.sigla",
				sortable: false,
				searchable: false,
				label: "Estado",
			},
			{
				key: "id_estado",
				sortable: false,
				searchable: true,
				label: "ID Estado",
			},
			{
				key: "nome",
				sortable: true,
				searchable: true,
				label: "Nome",
			},
			{
				key: "dt_criacao",
				sortable: false,
				searchable: false,
				label: "Dt. Criação",
			},
			{
				key: "dt_alteracao",
				sortable: false,
				searchable: false,
				label: "Dt. Alteração",
			},
		];

		const camposListaRegistros = Array.from(fields);
		camposListaRegistros.splice(2, 1);

		const provedorListaRegistros = new ItemsProvider({
			axios: axios,
			fields: fields,
			searchFields: {
				id_estado: {
					value: null,
				},
			},
		});
		provedorListaRegistros.state.filterIgnoredFields.push("acoes");
		provedorListaRegistros.onResponseError = this.onListaRegistrosErroResposta;
		provedorListaRegistros.busy = true;
		return {
			obtendoListas: false,
			listaEstado: [],
			mensagemErro: "",
			registroSelecionado: null,
			removerRegistroDialogoStatus: false,
			scrollSettings: {
				maxScrollbarLength: 60,
				wheelSpeed: 0.3,
			},
			camposListaRegistros: camposListaRegistros,
			provedorListaRegistrosFiltro: "",
			provedorListaRegistrosFiltroGlobal: false,
			provedorListaRegistros: provedorListaRegistros,
			listaRegistrosPaginaTotal:
				provedorListaRegistros.state.perPage > 0
					? provedorListaRegistros.totalRows >=
					  provedorListaRegistros.state.perPage
						? Math.ceil(
								provedorListaRegistros.totalRows /
									provedorListaRegistros.state.perPage
						  )
						: 1
					: 1,
		};
	},
	watch: {
		"provedorListaRegistros.state.searchFields.id_estado.value"(val) {
			this.atualizarListaRegistrosAtrasada();
		},
		provedorListaRegistrosFiltro(val) {
			this.filtrarListaRegistros(val);
		},
	},
	created() {
		this.filtrarListaRegistros = this.lodash.debounce((val) => {
			this.provedorListaRegistrosFiltroGlobal = true;
			this.provedorListaRegistros.state.filter = val;
		}, 200);
		this.atualizarListaRegistrosAtrasada = this.lodash.debounce(
			this.atualizarListaRegistros,
			200
		);
	},
	mounted() {
		this.$parent.backendOcupado = true;
		this.obtendoListas = true;
		this.provedorListaRegistros.busy = true;
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
						this.listaEstado = response.data.estados;
					} else {
						this.$vs.notify({
							title: "Erro",
							text:
								"Incapaz de carregar a lista de estados.",
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
				this.$parent.backendOcupado = false;
				this.provedorListaRegistros.busy = false;
				this.$nextTick(() => {
					this.atualizarListaRegistros();
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
				this.obtendoListas = false;
				this.$parent.backendOcupado = false;
				this.provedorListaRegistros.busy = false;
				this.$nextTick(() => {
					this.atualizarListaRegistros();
				});
			});
	},
	methods: {
		cancelarRemoverRegistro() {
			if (
				this.$parent.backendOcupado ||
				this.provedorListaRegistros.busy
			) {
				this.removerRegistroDialogoStatus = true;
				return;
			}
			this.registroSelecionado = null;
			this.mudarListaRegistrosOrdenacaoStatus(true);
		},
		fecharRemoverRegistro(evt) {
			if (
				this.$parent.backendOcupado ||
				this.provedorListaRegistros.busy ||
				typeof evt == "undefined"
			) {
				this.removerRegistroDialogoStatus = true;
				return;
			} else {
				this.removerRegistroDialogoStatus = false;
			}
			this.mudarListaRegistrosOrdenacaoStatus(true);
		},
		removerRegistro(item, index, button) {
			if (
				this.provedorListaRegistros.busy ||
				this.$parent.backendOcupado ||
				this.removerRegistroDialogoStatus
			) {
				return;
			}
			this.mudarListaRegistrosOrdenacaoStatus(false);
			this.mensagemErro = "";
			this.registroSelecionado = item;
			this.removerRegistroDialogoStatus = true;
			this.$nextTick(() => {
				setTimeout(() => {
					window
						.jQuery(
							".vs-dialog-cancel-button",
							this.$refs.removerRegistroDialogo.$refs.dialogx
						)
						.trigger("focus");
				}, 150);
			});
		},
		mudarListaRegistrosOrdenacaoStatus(status) {
			if (status) {
				window
					.jQuery("#listaRegistros", this.$refs.listaRegistros.$el)
					.attr("aria-busy", "false");
			} else {
				window
					.jQuery("#listaRegistros", this.$refs.listaRegistros.$el)
					.attr("aria-busy", "true");
			}
		},
		mudarRemoverRegistroDialogoStatus(status) {
			if (status) {
				window
					.jQuery(
						".vs-dialog-cancel",
						this.$refs.removerRegistroDialogo.$refs.dialogx
					)
					.removeClass("disabled");
				window
					.jQuery(
						".vs-dialog-dark",
						this.$refs.removerRegistroDialogo.$refs.con
					)
					.removeClass("disabled");
				window
					.jQuery(
						".vs-dialog-cancel-button",
						this.$refs.removerRegistroDialogo.$refs.dialogx
					)
					.attr("disabled", null);
			} else {
				window
					.jQuery(
						".vs-dialog-cancel",
						this.$refs.removerRegistroDialogo.$refs.dialogx
					)
					.addClass("disabled");
				window
					.jQuery(
						".vs-dialog-dark",
						this.$refs.removerRegistroDialogo.$refs.con
					)
					.addClass("disabled");
				window
					.jQuery(
						".vs-dialog-cancel-button",
						this.$refs.removerRegistroDialogo.$refs.dialogx
					)
					.attr("disabled", "disabled");
			}
		},
		editarRegistro(item, index, button) {
			if (
				this.provedorListaRegistros.busy ||
				this.$parent.backendOcupado ||
				this.removerRegistroDialogoStatus
			) {
				return;
			}
			this.dialogoEditarRegistroAbrir(item);
		},
		historicoRegistro(item, index, button) {
			if (
				this.provedorListaRegistros.busy ||
				this.$parent.backendOcupado ||
				this.removerRegistroDialogoStatus
			) {
				return;
			}
			this.dialogoHistoricoRegistroAbrir(item);
		},
		historicoRegistroRemovido() {
			if (
				this.provedorListaRegistros.busy ||
				this.$parent.backendOcupado ||
				this.removerRegistroDialogoStatus
			) {
				return;
			}
			this.dialogoHistoricoRegistroRemovidoAbrir();
		},
		atualizarListaRegistros() {
			if (
				this.provedorListaRegistros.busy ||
				this.$parent.backendOcupado
			) {
				return;
			}
			this.$refs.listaRegistros.refresh();
		},
		onListaRegistrosAtualizado() {
			this.listaRegistrosPaginaTotal =
				this.provedorListaRegistros.state.perPage > 0
					? this.provedorListaRegistros.totalRows >=
					  this.provedorListaRegistros.state.perPage
						? Math.ceil(
								this.provedorListaRegistros.totalRows /
									this.provedorListaRegistros.state.perPage
						  )
						: 1
					: 1;
			if (this.provedorListaRegistrosFiltroGlobal) {
				this.provedorListaRegistrosFiltroGlobal = false;
				this.$nextTick(() => {
					setTimeout(() => {
						window
							.jQuery(
								this.$refs
									.provedorListaRegistrosFiltroGlobalCampo
									.$refs.vsinput
							)
							.trigger("focus");
					}, 150);
				});
			}
		},
		onListaRegistrosTamanhoPagina(size) {
			this.provedorListaRegistros.state.currentPage = 1;
		},
		onListaRegistrosErroResposta(error) {
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
				this.provedorListaRegistros.busy = true;
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
		},
		processarRemoverRegistro() {
			this.removerRegistroDialogoStatus = true;
			if (
				this.provedorListaRegistros.busy ||
				this.$parent.backendOcupado ||
				!this.registroSelecionado ||
				!this.registroSelecionado.id
			) {
				return;
			}
			this.$parent.backendOcupado = true;
			this.mensagemErro = "";
			this.mudarRemoverRegistroDialogoStatus(false);
			axios
				.delete(this.backendUrl + `/${this.registroSelecionado.id}`)
				.then((response) => {
					if (
						response &&
						response.status == 200 &&
						response.data &&
						response.data.status == "removido"
					) {
						this.$vs.notify({
							title: "Sucesso",
							text: `${this.backendModel} removida.`,
							iconPack: "feather",
							icon: "icon-check",
							color: "success",
						});
						this.$parent.backendOcupado = false;
						this.mudarListaRegistrosOrdenacaoStatus(true);
						this.removerRegistroDialogoStatus = false;
						this.registroSelecionado = null;
						this.atualizarListaRegistros();
					} else {
						this.$vs.notify({
							title: "Erro",
							text: "Dados de resposta do registro inválidos.",
							iconPack: "feather",
							icon: "icon-alert-circle",
							color: "danger",
						});
						this.$parent.backendOcupado = false;
						this.mudarRemoverRegistroDialogoStatus(true);
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
							error.response.data.errors instanceof Array ||
							error.response.data.errors instanceof Object
						) {
							var erros = {};
							Object.keys(error.response.data.errors).forEach((key) => {
									if (
										error.response.data.errors[key] &&
										error.response.data.errors[key].length >
											0 &&
										error.response.data.errors[key][0] &&
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
								}
							);
						} else {
							this.mensagemErro = error.response.data.errors.toString();
						}
					}
					this.$parent.backendOcupado = false;
					this.mudarRemoverRegistroDialogoStatus(true);
				});
		},
	},
};
</script>
<style scoped lang="scss">
@import "./configs.scss";

.pagina#{$nomePagina} {
	.bootstrap-inside {
		td div.con-vs-tooltip {
			display: inline-block;
		}
		td div.con-vs-tooltip.disabled {
			pointer-events: none !important;
		}
		.dataTablesButton {
			direction: ltr;
			--vs-light: 245, 245, 245;
			--vs-primary: 115, 103, 240;
			--vs-success: 40, 199, 111;
			--vs-danger: 234, 84, 85;
			--vs-warning: 255, 159, 67;
			--vs-dark: 30, 30, 30;
			--vh: 9.37px;
			margin: 0;
			outline: none;
			text-transform: none;
			text-decoration: none;
			transition: all 0.2s ease;
			border: 0;
			border-radius: 6px;
			cursor: pointer;
			position: relative;
			overflow: hidden;
			/*color: #fff;*/
			box-sizing: border-box;
			display: inline-block;
			-webkit-box-align: center;
			align-items: center;
			-webkit-box-pack: center;
			justify-content: center;
			font-family: "Montserrat", Helvetica, Arial, sans-serif;
			padding: 7px;
			font-size: 0.7em;
			width: 28px !important;
			height: 28px !important;
			float: none;
			border-collapse: separate;
			line-height: normal;
			-webkit-appearance: button;
			font-weight: normal;
		}
	}
	div.vs-row.listaRegistrosPaginacao {
		width: auto;
	}
	div.vs-row.listaRegistrosPaginacao:disabled,
	div.vs-row.listaRegistrosPaginacao.disabled,
	div.vs-row.listaRegistrosPaginacao:disabled *,
	div.vs-row.listaRegistrosPaginacao.disabled * {
		cursor: default;
		pointer-events: none;
		opacity: 0.5;
	}
}
</style>
<style lang="scss">
@import "./configs.scss";

.pagina#{$nomePagina} .bootstrap-inside {
	@import "bootstrap/scss/bootstrap";
	@import "bootstrap-vue/src/index.scss";
	table.table.b-table {
		border: 2px solid #f8f8f8 !important;
	}
	table.table.b-table thead tr th {
		border: 0px !important;
	}
	table.table.b-table tbody tr td {
		border: 0px !important;
	}
	table.table.b-table.table-striped tbody tr:nth-of-type(odd) {
		background-color: #f8f8f8 !important;
	}
	table.table.b-table.table-sm tbody tr.b-table-busy-slot td {
		padding: 0px !important;
	}
	table.table.b-table.table-sm th,
	table.table.b-table.table-sm td {
		padding: 10px 15px !important;
	}
	table.table.b-table tbody tr.b-table-busy-slot .vs-con-loading__container {
		min-height: 60px !important;
	}
	table.table.b-table
		tbody
		tr.b-table-busy-slot
		.vs-con-loading__container
		.con-vs-loading {
		z-index: 52004 !important;
	}
	table.table.b-table[aria-busy="true"] {
		opacity: 1 !important;
	}
	table.table.b-table[aria-busy="true"] > thead > tr > [aria-sort],
	table.table.b-table[aria-busy="true"] > tfoot > tr > [aria-sort] {
		cursor: default !important;
		pointer-events: none !important;
	}
	.dataTablesButton i.vs-icon {
		font-size: 14px;
	}
	.dataTablesButton.registroRemovido:disabled {
		opacity: 1;
	}
}
.pagina#{$nomePagina} {
	div.listagemEstado ul.vs__dropdown-menu {
		max-height: 200px;
	}
}
div.con-vs-dropdown--menu.vs-dropdown-menu
	ul.vs-component.vs-dropdown--menu
	li.vs-component.vs-dropdown--item {
	width: 100%;
	margin: 0px;
}
div.con-vs-dropdown--menu.vs-dropdown-menu
	ul.vs-component.vs-dropdown--menu
	li.vs-component.vs-dropdown--item
	a.vs-dropdown--item-link:hover {
	background: rgba(var(--vs-primary), 1) !important;
	color: white !important;
}
button.vs-con-dropdown.disabled > div.flex {
	background-color: #f8f8f8 !important;
}
.pagina#{$nomePagina}Dialogo.con-vs-dialog.editar-dialogo
	.vs-dialog-dark.disabled {
	pointer-events: none !important;
}
.pagina#{$nomePagina}Dialogo.con-vs-dialog.editar-dialogo .vs-dialog {
	max-width: 530px;
}
.pagina#{$nomePagina}Dialogo.con-vs-dialog.editar-dialogo
	.vs-dialog
	header
	.vs-dialog-cancel.disabled {
	pointer-events: none !important;
}
[dir]
	.pagina#{$nomePagina}Dialogo.con-vs-dialog.editar-dialogo
	.vs-dialog
	.vs-dialog-text {
	padding: 0;
}
.pagina#{$nomePagina}Dialogo.con-vs-dialog.editar-dialogo
	.vs-dialog
	.vs-dialog-text
	.scroll-area {
	max-height: 75vh;
}
.pagina#{$nomePagina}Dialogo.con-vs-dialog.editar-dialogo .vs-dialog footer {
	display: flow-root;
}
.pagina#{$nomePagina}Dialogo.con-vs-dialog.editar-dialogo
	.vs-dialog
	footer
	button.vs-dialog-accept-button,
.pagina#{$nomePagina}Dialogo.con-vs-dialog.editar-dialogo
	.vs-dialog
	footer
	button.vs-dialog-cancel-button {
	float: right;
}
.pagina#{$nomePagina}Dialogo.con-vs-dialog.editar-dialogo
	.vs-dialog
	footer
	button.vs-dialog-accept-button {
	margin-left: 0.5rem !important;
}
.pagina#{$nomePagina}Dialogo.con-vs-dialog.editar-dialogo
	.vs-dialog
	footer
	button.vs-dialog-cancel-button {
	color: rgba(var(--vs-danger), 1) !important;
}
.pagina#{$nomePagina}Dialogo.con-vs-dialog.editar-dialogo
	.vs-dialog
	footer
	button.vs-dialog-cancel-button:hover {
	background: rgba(var(--vs-danger), 0.08) !important;
}
</style>
