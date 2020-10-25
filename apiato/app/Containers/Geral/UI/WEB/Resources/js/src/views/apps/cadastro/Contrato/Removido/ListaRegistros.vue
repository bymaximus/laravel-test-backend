<template>
	<div class="vx-card p-6">
		<vs-row class="mb-2">
			<vs-col
				vs-type="flex"
				vs-align="flex-end"
				vs-justify="flex-end"
				vs-w="12"
			>
				<vx-input-group vs-justify="flex-end">
					<vs-input
						icon-pack="feather"
						icon="icon-search"
						placeholder="Procurar"
						v-model="provedorListaRegistrosFiltro"
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
					color="warning"
					type="filled"
					class="ml-2"
					:disabled="provedorListaRegistros.busy || $parent.backendOcupado"
					@click="cancelar"
				>Voltar</vs-button>
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
				:fields="provedorListaRegistros.fields"
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
				<template v-slot:cell(dt_remocao)="row">
					<span class="text-nowrap">{{ row.value }}</span>
				</template>
				<template v-slot:cell(email)="row">
					<span class="text-nowrap">{{ row.value }}</span>
				</template>
				<template v-slot:cell(nome)="row">
					<span class="text-break">{{ row.value }}</span>
				</template>
				<template v-slot:cell(endereco)="row">
					<span class="text-break">{{ row.value }}</span>
				</template>
				<template v-slot:cell(acoes)="row">
					<span class="text-nowrap">
						<vx-tooltip
							color="warning"
							position="right"
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
								:disabled="provedorListaRegistros.busy || $parent.backendOcupado"
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

export default {
	components: {
		"v-select": vSelect,
	},
	props: {
		dialogoHistoricoRegistroAbrir: {
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
				key: "email",
				sortable: true,
				searchable: true,
				label: "Email",
			},
			{
				key: "nome",
				sortable: true,
				searchable: true,
				label: "Nome",
			},
			{
				key: "endereco",
				sortable: false,
				searchable: false,
				label: "Endereço",
			},
			{
				key: "dt_remocao",
				sortable: false,
				searchable: false,
				label: "Dt. Remoção",
			},
		];
		const provedorListaRegistros = new ItemsProvider({
			axios: axios,
			fields: fields,
		});
		provedorListaRegistros.state.filterIgnoredFields.push("acoes");
		provedorListaRegistros.onResponseError = this.onListaRegistrosErroResposta;
		return {
			mensagemErro: "",
			registroSelecionado: null,
			provedorListaRegistrosFiltro: "",
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
		provedorListaRegistrosFiltro(val) {
			this.filtrarListaRegistros(val);
		},
	},
	created() {
		this.filtrarListaRegistros = this.lodash.debounce((val) => {
			this.provedorListaRegistros.state.filter = val;
		}, 200);
	},
	methods: {
		cancelar() {
			if (
				this.provedorListaRegistros.busy ||
				this.$parent.backendOcupado
			) {
				return;
			}
			this.$parent.backendOcupado = true;
			this.$router.push(this.$parent.configuracaoPagina.routerPath);
		},
		historicoRegistro(item, index, button) {
			if (
				this.provedorListaRegistros.busy ||
				this.$parent.backendOcupado
			) {
				return;
			}
			this.dialogoHistoricoRegistroAbrir(item);
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
	},
};
</script>
<style scoped lang="scss">
@import "./../configs.scss";

.pagina#{$nomePagina}Removido {
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
@import "./../configs.scss";

.pagina#{$nomePagina}Removido .bootstrap-inside {
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
</style>
