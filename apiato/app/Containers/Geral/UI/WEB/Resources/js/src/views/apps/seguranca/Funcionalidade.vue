<template>
	<div class="vx-card p-6 paginaSegurancaFuncionalidade">
		<vs-prompt
			class="paginaSegurancaFuncionalidade editar-dialogo"
			title="Funcionalidade Nome"
			accept-text="Renomear"
			cancel-text="Cancelar"
			button-accept="filled"
			button-cancel="flat"
			type="confirm"
			@accept="processarFuncionalidadeRenomear"
			@cancel="cancelarFuncionalidadeRenomear"
			@close="fecharFuncionalidadeRenomear"
			:is-valid="!formularioValidoRenomear && ! backendOcupado"
			:active.sync="funcionalidadeRenomearDialogoStatus"
			ref="funcionalidadeRenomearDialogo"
		>
			<VuePerfectScrollbar
				class="scroll-area p-4"
				:settings="scrollSettings"
				:key="$vs.rtl"
			>
				<form
					@submit.prevent
					data-vv-scope="formularioRenomear"
				>
					<div class="vx-row">
						<div class="vx-col w-full mb-3">
							<vs-input
								name="nome"
								label-placeholder="Nome"
								v-model="camposEditar.nome"
								class="w-full campoRequerido"
								v-validate="'required|min:1|max:100'"
								val-icon-danger="clear"
								val-icon-success="done"
								data-vv-as="Nome"
								:danger="errors.has('formularioRenomear.nome')"
								:success="!errors.has('formularioRenomear.nome') && camposEditar.nome != ''"
								:color="!errors.has('formularioRenomear.nome') ? 'success' : 'danger'"
								:disabled="backendOcupado"
								@keyup.enter.native="processarFuncionalidadeRenomear"
								@keyup.esc.native="fecharFuncionalidadeRenomear(true)"
							/>
							<span
								class="text-danger text-sm mt-2"
								v-show="errors.has('formularioRenomear.nome')"
								:class="[
                                    {'block' : errors.has('formularioRenomear.nome')}
                                ]"
							>{{ errors.first('formularioRenomear.nome') }}</span>
						</div>
					</div>
				</form>
				<vs-alert
					:active="(mensagemErro && mensagemErro != '' ? true : false)"
					color="danger"
					class="mb-10"
				>
					<span v-html="mensagemErro"></span>
				</vs-alert>
			</VuePerfectScrollbar>
		</vs-prompt>
		<vs-prompt
			class="paginaSegurancaFuncionalidade editar-dialogo"
			title="Funcionalidade URL"
			accept-text="Salvar URL"
			cancel-text="Cancelar"
			button-accept="filled"
			button-cancel="flat"
			type="confirm"
			@accept="processarFuncionalidadeURL"
			@cancel="cancelarFuncionalidadeURL"
			@close="fecharFuncionalidadeURL"
			:is-valid="!formularioValidoURL && ! backendOcupado"
			:active.sync="funcionalidadeURLDialogoStatus"
			ref="funcionalidadeURLDialogo"
		>
			<VuePerfectScrollbar
				class="scroll-area p-4"
				:settings="scrollSettings"
				:key="$vs.rtl"
			>
				<form
					@submit.prevent
					data-vv-scope="formularioURL"
				>
					<div class="vx-row">
						<div class="vx-col w-full mb-3">
							<vs-input
								name="url"
								label-placeholder="URL"
								v-model="camposEditar.url"
								class="w-full"
								val-icon-danger="clear"
								val-icon-success="done"
								data-vv-as="URL"
								:danger="errors.has('formularioURL.url')"
								:success="!errors.has('formularioURL.url')"
								:color="!errors.has('formularioURL.url') ? 'success' : 'danger'"
								:disabled="backendOcupado"
								@keyup.enter.native="processarFuncionalidadeURL"
								@keyup.esc.native="fecharFuncionalidadeURL(true)"
							/>
							<span
								class="text-danger text-sm mt-2"
								v-show="errors.has('formularioURL.url')"
								:class="[
                                    {'block' : errors.has('formularioURL.url')}
                                ]"
							>{{ errors.first('formularioURL.url') }}</span>
						</div>
					</div>
				</form>
				<vs-alert
					:active="(mensagemErro && mensagemErro != '' ? true : false)"
					color="danger"
					class="mb-10"
				>
					<span v-html="mensagemErro"></span>
				</vs-alert>
			</VuePerfectScrollbar>
		</vs-prompt>
		<vs-prompt
			class="paginaSegurancaFuncionalidade editar-dialogo"
			title="Funcionalidade Ícone"
			accept-text="Salvar Ícone"
			cancel-text="Cancelar"
			button-accept="filled"
			button-cancel="flat"
			type="confirm"
			@accept="processarFuncionalidadeIcone"
			@cancel="cancelarFuncionalidadeIcone"
			@close="fecharFuncionalidadeIcone"
			:is-valid="!formularioValidoIcone && ! backendOcupado"
			:active.sync="funcionalidadeIconeDialogoStatus"
			ref="funcionalidadeIconeDialogo"
		>
			<VuePerfectScrollbar
				class="scroll-area p-4"
				:settings="scrollSettings"
				:key="$vs.rtl"
			>
				<form
					@submit.prevent
					data-vv-scope="formularioIcone"
				>
					<div class="vx-row">
						<div class="vx-col w-full mb-3">
							<vs-input
								name="icone"
								label-placeholder="Ícone"
								v-model="camposEditar.icone"
								class="w-full"
								val-icon-danger="clear"
								val-icon-success="done"
								data-vv-as="Ícone"
								:danger="errors.has('formularioIcone.icone')"
								:success="!errors.has('formularioIcone.icone')"
								:color="!errors.has('formularioIcone.icone') ? 'success' : 'danger'"
								:disabled="backendOcupado"
								@keyup.enter.native="processarFuncionalidadeIcone"
								@keyup.esc.native="fecharFuncionalidadeIcone(true)"
							/>
							<span
								class="text-danger text-sm mt-2"
								v-show="errors.has('formularioIcone.icone')"
								:class="[
                                    {'block' : errors.has('formularioIcone.icone')}
                                ]"
							>{{ errors.first('formularioIcone.icone') }}</span>
						</div>
					</div>
				</form>
				<vs-alert
					:active="(mensagemErro && mensagemErro != '' ? true : false)"
					color="danger"
					class="mb-10"
				>
					<span v-html="mensagemErro"></span>
				</vs-alert>
			</VuePerfectScrollbar>
		</vs-prompt>
		<vs-prompt
			class="paginaSegurancaFuncionalidade editar-dialogo"
			title="Remover Funcionalidade"
			accept-text="Remover"
			cancel-text="Cancelar"
			button-accept="filled"
			button-cancel="flat"
			type="confirm"
			color="danger"
			@accept="processarFuncionalidadeRemover"
			@cancel="cancelarFuncionalidadeRemover"
			@close="fecharFuncionalidadeRemover"
			@keyup.esc.native="fecharFuncionalidadeRemover(true)"
			:is-valid="! backendOcupado"
			:active.sync="funcionalidadeRemoverDialogoStatus"
			ref="funcionalidadeRemoverDialogo"
		>
			<VuePerfectScrollbar
				class="scroll-area p-4"
				:settings="scrollSettings"
				:key="$vs.rtl"
			>
				Tem certeza que deseja remover essa funcionalidade?
				<vs-alert
					:active="(mensagemErro && mensagemErro != '' ? true : false)"
					color="danger"
					class="mb-10"
				>
					<span v-html="mensagemErro"></span>
				</vs-alert>
			</VuePerfectScrollbar>
		</vs-prompt>
		<vs-prompt
			class="paginaSegurancaFuncionalidade editar-dialogo"
			title="Adicionar Funcionalidade"
			accept-text="Adicionar"
			cancel-text="Cancelar"
			button-accept="filled"
			button-cancel="flat"
			type="confirm"
			@accept="processarFuncionalidadeAdicionar"
			@cancel="cancelarFuncionalidadeAdicionar"
			@close="fecharFuncionalidadeAdicionar"
			:is-valid="!formularioValidoAdicionar && ! backendOcupado"
			:active.sync="funcionalidadeAdicionarDialogoStatus"
			ref="funcionalidadeAdicionarDialogo"
		>
			<VuePerfectScrollbar
				class="scroll-area p-4"
				:settings="scrollSettings"
				:key="$vs.rtl"
			>
				<form
					@submit.prevent
					data-vv-scope="formularioAdicionar"
				>
					<div class="vx-row">
						<div class="vx-col w-full mb-3">
							<vs-input
								name="nome"
								label-placeholder="Nome"
								v-model="camposAdicionar.nome"
								class="w-full campoRequerido"
								v-validate="'required|min:1|max:100'"
								val-icon-danger="clear"
								val-icon-success="done"
								data-vv-as="Nome"
								:danger="errors.has('formularioAdicionar.nome')"
								:success="!errors.has('formularioAdicionar.nome') && camposAdicionar.nome != ''"
								:color="!errors.has('formularioAdicionar.nome') ? 'success' : 'danger'"
								:disabled="backendOcupado"
								@keyup.esc.native="fecharFuncionalidadeAdicionar(true)"
							/>
							<span
								class="text-danger text-sm mt-2"
								v-show="errors.has('formularioAdicionar.nome')"
								:class="[
                                    {'block' : errors.has('formularioAdicionar.nome')}
                                ]"
							>{{ errors.first('formularioAdicionar.nome') }}</span>
						</div>
					</div>
					<div class="vx-row">
						<div class="vx-col w-full mb-3">
							<vs-input
								name="url"
								label-placeholder="URL"
								v-model="camposAdicionar.url"
								class="w-full"
								val-icon-danger="clear"
								val-icon-success="done"
								data-vv-as="URL"
								:danger="errors.has('formularioAdicionar.url')"
								:success="!errors.has('formularioAdicionar.url')"
								:color="!errors.has('formularioAdicionar.url') ? 'success' : 'danger'"
								:disabled="backendOcupado"
								@keyup.esc.native="fecharFuncionalidadeAdicionar(true)"
							/>
							<span
								class="text-danger text-sm mt-2"
								v-show="errors.has('formularioAdicionar.url')"
								:class="[
                                    {'block' : errors.has('formularioAdicionar.url')}
                                ]"
							>{{ errors.first('formularioAdicionar.url') }}</span>
						</div>
					</div>
					<div class="vx-row">
						<div class="vx-col w-full mb-3">
							<vs-input
								name="icone"
								label-placeholder="Ícone"
								v-model="camposAdicionar.icone"
								class="w-full"
								val-icon-danger="clear"
								val-icon-success="done"
								data-vv-as="Ícone"
								:danger="errors.has('formularioAdicionar.icone')"
								:success="!errors.has('formularioAdicionar.icone')"
								:color="!errors.has('formularioAdicionar.icone') ? 'success' : 'danger'"
								:disabled="backendOcupado"
								@keyup.enter.native="processarFuncionalidadeAdicionar"
								@keyup.esc.native="fecharFuncionalidadeAdicionar(true)"
							/>
							<span
								class="text-danger text-sm mt-2"
								v-show="errors.has('formularioAdicionar.icone')"
								:class="[
                                    {'block' : errors.has('formularioAdicionar.icone')}
                                ]"
							>{{ errors.first('formularioAdicionar.icone') }}</span>
						</div>
					</div>
				</form>
				<vs-alert
					:active="(mensagemErro && mensagemErro != '' ? true : false)"
					color="danger"
					class="mb-10"
				>
					<span v-html="mensagemErro"></span>
				</vs-alert>
			</VuePerfectScrollbar>
		</vs-prompt>
		<v-jstree
			:data="funcionalidades"
			@item-click="onItemClick"
			@item-toggle="onItemToggle"
			@item-drag-start="onItemDragStart"
			@item-drag-end="onItemDragEnd"
			@item-drop-before="onItemDropBefore"
			@item-drop="onItemDrop"
			@item-drop-sibling-left="onItemDropSiblingLeft"
			@item-drop-sibling-right="onItemDropSiblingRight"
			ref="menuList"
			loading-text="Carregando..."
			text-field-name="nome"
			value-field-name="codigo"
			:draggable="true"
			:expand-timer="true"
			:execute-sibling-movement="true"
		>
			<template slot-scope="_">
				<div
					style="display: inherit; width: 200px"
					@contextmenu.prevent="$refs.menuContexto.open($event, _.model)"
					v-on:dblclick="onItemDoubleClick(_.model)"
				>
					<i
						:class="_.vm.themeIconClasses"
						role="presentation"
						v-if="!_.model.loading"
					></i>
					{{_.model.nome}}
				</div>
			</template>
		</v-jstree>
		<vue-context
			ref="menuContexto"
			@close="onMenuContextoFechar"
			@open="onMenuContextoAbrir"
		>
			<template
				slot-scope="child"
				v-if="child.data"
			>
				<li
					v-if="child.data.codigo > 0 && child.data.id > 0"
					class="cursor-pointer hover:bg-primary hover:text-white"
				>
					<a
						@click.prevent="funcionalidadeRenomear(child.data)"
						class="flex items-center text-sm"
					>
						<feather-icon
							icon="EditIcon"
							svgClasses="w-5 h-5"
						/>
						<span class="ml-2">Renomear</span>
					</a>
				</li>
				<li
					v-if="child.data.codigo > 0 && child.data.id > 0"
					class="cursor-pointer hover:bg-primary hover:text-white"
				>
					<a
						@click.prevent="funcionalidadeURL(child.data)"
						class="flex items-center text-sm"
					>
						<feather-icon
							icon="LinkIcon"
							svgClasses="w-5 h-5"
						/>
						<span class="ml-2">URL</span>
					</a>
				</li>
				<li
					v-if="child.data.codigo > 0 && child.data.id > 0"
					class="cursor-pointer hover:bg-primary hover:text-white"
				>
					<a
						@click.prevent="funcionalidadeIcone(child.data)"
						class="flex items-center text-sm"
					>
						<feather-icon
							icon="ImageIcon"
							svgClasses="w-5 h-5"
						/>
						<span class="ml-2">Ícone</span>
					</a>
				</li>
				<li v-if="child.data.id > 0 && child.data.codigo > 0">
					<vs-divider />
				</li>
				<li
					v-if="!child.data.url && child.data.id > 0"
					class="cursor-pointer hover:bg-primary hover:text-white"
				>
					<a
						@click.prevent="funcionalidadeAdicionar(child.data)"
						class="flex items-center text-sm"
					>
						<feather-icon
							icon="PlusIcon"
							svgClasses="w-5 h-5"
						/>
						<span class="ml-2">Adicionar</span>
					</a>
				</li>
				<li
					v-if="child.data.codigo > 0 && child.data.id > 0"
					class="cursor-pointer hover:bg-danger hover:text-white"
				>
					<a
						@click.prevent="funcionalidadeRemover(child.data)"
						class="flex items-center text-sm menuItemRemover"
					>
						<feather-icon
							icon="TrashIcon"
							svgClasses="w-5 h-5"
						/>
						<span class="ml-2">Remover</span>
					</a>
				</li>
			</template>
		</vue-context>
	</div>
</template>

<script>
import Vue from "vue";
import { BootstrapVue } from "bootstrap-vue";
import VuePerfectScrollbar from "vue-perfect-scrollbar";

//import VJstree from "vue-jstree";
import VJstree from "vue-jstree-extended";

import { VueContext } from "vue-context";
import "vue-context/dist/css/vue-context.css";

import axios from "@/axios.js";

Vue.use(BootstrapVue);

export default {
	$_veeValidate: {
		validator: "new",
	},
	components: {
		VJstree,
		VueContext,
		VuePerfectScrollbar,
	},
	data() {
		return {
			backendUrl: this.$urlBasePath + "seguranca/funcionalidade",
			backendModel: "Funcionalidade",
			backendOcupado: false,
			mensagemErro: "",
			funcionalidades: [],
			registroSelecionado: null,
			scrollSettings: {
				maxScrollbarLength: 60,
				wheelSpeed: 0.3,
			},
			funcionalidadeRenomearDialogoStatus: false,
			funcionalidadeURLDialogoStatus: false,
			funcionalidadeIconeDialogoStatus: false,
			funcionalidadeRemoverDialogoStatus: false,
			funcionalidadeAdicionarDialogoStatus: false,
			camposEditar: {
				id: null,
				nome: "",
				url: "",
				icone: "",
			},
			camposAdicionar: {
				idParente: null,
				nome: "",
				url: "",
				icone: "",
			},
		};
	},
	computed: {
		formularioValidoRenomear() {
			var invalido = this.errors.any("formularioRenomear");
			if (!invalido && this.fields.$formularioRenomear) {
				invalido = Object.keys(this.fields.$formularioRenomear).some((key) => {
						if (this.fields.$formularioRenomear[key].invalid) {
							return true;
						} else if (
							!this.fields.$formularioRenomear[key].valid
						) {
							return true;
						}
					}
				);
			}
			return invalido;
		},
		formularioValidoURL() {
			var invalido = this.errors.any("formularioURL");
			if (!invalido && this.fields.$formularioURL) {
				invalido = Object.keys(this.fields.$formularioURL).some((key) => {
						if (this.fields.$formularioURL[key].invalid) {
							return true;
						} else if (!this.fields.$formularioURL[key].valid) {
							return true;
						}
					}
				);
			}
			return invalido;
		},
		formularioValidoIcone() {
			var invalido = this.errors.any("formularioIcone");
			if (!invalido && this.fields.$formularioIcone) {
				invalido = Object.keys(this.fields.$formularioIcone).some((key) => {
						if (this.fields.$formularioIcone[key].invalid) {
							return true;
						} else if (!this.fields.$formularioIcone[key].valid) {
							return true;
						}
					}
				);
			}
			return invalido;
		},
		formularioValidoAdicionar() {
			var invalido = this.errors.any("formularioAdicionar");
			if (!invalido && this.fields.$formularioAdicionar) {
				invalido = Object.keys(this.fields.$formularioAdicionar).some((key) => {
						if (this.fields.$formularioAdicionar[key].invalid) {
							return true;
						} else if (
							!this.fields.$formularioAdicionar[key].valid
						) {
							return true;
						}
					}
				);
			}
			return invalido;
		},
		camposFormularioRenomear() {
			return {
				nome: this.camposEditar.nome,
			};
		},
		camposFormularioURL() {
			return {
				url: this.camposEditar.url,
			};
		},
		camposFormularioIcone() {
			return {
				icone: this.camposEditar.icone,
			};
		},
		camposFormularioAdicionar() {
			return {
				id_parente: this.camposAdicionar.idParente,
				nome: this.camposAdicionar.nome,
				icone: this.camposAdicionar.icone,
				url: this.camposAdicionar.url,
			};
		},
	},
	methods: {
		desmarcarFuncionalidade(funcionalidade) {
			if (funcionalidade) {
				funcionalidade.selected = false;
				if (
					funcionalidade.hasOwnProperty("children") &&
					funcionalidade.children &&
					funcionalidade.children.length > 0
				) {
					funcionalidade.children.forEach((children) => {
						if (children) {
							this.desmarcarFuncionalidade(children);
						}
					});
				}
			}
		},
		localizarItem(funcionalidade, itemIndex) {
			if (funcionalidade) {
				if (
					funcionalidade.hasOwnProperty("id") &&
					funcionalidade.id == itemIndex
				) {
					return funcionalidade;
				} else if (
					funcionalidade.hasOwnProperty("children") &&
					funcionalidade.children &&
					funcionalidade.children.length > 0
				) {
					var item;
					for (var i in funcionalidade.children) {
						if (funcionalidade.children[i]) {
							item = this.localizarItem(
								funcionalidade.children[i],
								itemIndex
							);
							if (item) {
								if (item.id == itemIndex) {
									return funcionalidade;
								} else {
									return item;
								}
							}
						}
					}
				}
			}
		},
		_limparCamposFormularioRenomear() {
			this.camposEditar.id = null;
			this.camposEditar.nome = "";
		},
		_limparCamposFormularioURL() {
			this.camposEditar.id = null;
			this.camposEditar.url = "";
		},
		_limparCamposFormularioIcone() {
			this.camposEditar.id = null;
			this.camposEditar.icone = "";
		},
		_limparCamposFormularioRemover() {
			this.camposEditar.id = null;
		},
		_limparCamposFormularioAdicionar() {
			this.camposAdicionar.idParente = null;
			this.camposAdicionar.nome = "";
			this.camposAdicionar.url = "";
			this.camposAdicionar.icone = "";
		},
		limparCamposFormularioRenomear() {
			this.$nextTick(() => {
				this._limparCamposFormularioRenomear();
			});
		},
		limparCamposFormularioURL() {
			this.$nextTick(() => {
				this._limparCamposFormularioURL();
			});
		},
		limparCamposFormularioIcone() {
			this.$nextTick(() => {
				this._limparCamposFormularioIcone();
			});
		},
		limparCamposFormularioRemover() {
			this.$nextTick(() => {
				this._limparCamposFormularioRemover();
			});
		},
		limparCamposFormularioAdicionar() {
			this.$nextTick(() => {
				this._limparCamposFormularioAdicionar();
			});
		},
		cancelarFuncionalidadeRenomear() {
			if (this.backendOcupado) {
				this.funcionalidadeRenomearDialogoStatus = true;
				return;
			}
			this.limparCamposFormularioRenomear();
		},
		cancelarFuncionalidadeURL() {
			if (this.backendOcupado) {
				this.funcionalidadeURLDialogoStatus = true;
				return;
			}
			this.limparCamposFormularioURL();
		},
		cancelarFuncionalidadeIcone() {
			if (this.backendOcupado) {
				this.funcionalidadeIconeDialogoStatus = true;
				return;
			}
			this.limparCamposFormularioIcone();
		},
		cancelarFuncionalidadeRemover() {
			if (this.backendOcupado) {
				this.funcionalidadeRemoverDialogoStatus = true;
				return;
			}
			this.limparCamposFormularioRemover();
		},
		cancelarFuncionalidadeAdicionar() {
			if (this.backendOcupado) {
				this.funcionalidadeAdicionarDialogoStatus = true;
				return;
			}
			this.limparCamposFormularioAdicionar();
		},
		fecharFuncionalidadeRenomear(evt) {
			if (this.backendOcupado || typeof evt == "undefined") {
				this.funcionalidadeRenomearDialogoStatus = true;
			} else {
				this.funcionalidadeRenomearDialogoStatus = false;
			}
		},
		fecharFuncionalidadeURL(evt) {
			if (this.backendOcupado || typeof evt == "undefined") {
				this.funcionalidadeURLDialogoStatus = true;
			} else {
				this.funcionalidadeURLDialogoStatus = false;
			}
		},
		fecharFuncionalidadeIcone(evt) {
			if (this.backendOcupado || typeof evt == "undefined") {
				this.funcionalidadeIconeDialogoStatus = true;
			} else {
				this.funcionalidadeIconeDialogoStatus = false;
			}
		},
		fecharFuncionalidadeRemover(evt) {
			if (this.backendOcupado || typeof evt == "undefined") {
				this.funcionalidadeRemoverDialogoStatus = true;
			} else {
				this.funcionalidadeRemoverDialogoStatus = false;
			}
		},
		fecharFuncionalidadeAdicionar(evt) {
			if (this.backendOcupado || typeof evt == "undefined") {
				this.funcionalidadeAdicionarDialogoStatus = true;
			} else {
				this.funcionalidadeAdicionarDialogoStatus = false;
			}
		},
		mudarFuncionalidadeRenomearDialogoStatus(status) {
			if (status) {
				window
					.jQuery(
						".vs-dialog-cancel",
						this.$refs.funcionalidadeRenomearDialogo.$refs.dialogx
					)
					.removeClass("disabled");
				window
					.jQuery(
						".vs-dialog-dark",
						this.$refs.funcionalidadeRenomearDialogo.$refs.con
					)
					.removeClass("disabled");
				window
					.jQuery(
						".vs-dialog-cancel-button",
						this.$refs.funcionalidadeRenomearDialogo.$refs.dialogx
					)
					.attr("disabled", null);
			} else {
				window
					.jQuery(
						".vs-dialog-cancel",
						this.$refs.funcionalidadeRenomearDialogo.$refs.dialogx
					)
					.addClass("disabled");
				window
					.jQuery(
						".vs-dialog-dark",
						this.$refs.funcionalidadeRenomearDialogo.$refs.con
					)
					.addClass("disabled");
				window
					.jQuery(
						".vs-dialog-cancel-button",
						this.$refs.funcionalidadeRenomearDialogo.$refs.dialogx
					)
					.attr("disabled", "disabled");
			}
		},
		mudarFuncionalidadeURLDialogoStatus(status) {
			if (status) {
				window
					.jQuery(
						".vs-dialog-cancel",
						this.$refs.funcionalidadeURLDialogo.$refs.dialogx
					)
					.removeClass("disabled");
				window
					.jQuery(
						".vs-dialog-dark",
						this.$refs.funcionalidadeURLDialogo.$refs.con
					)
					.removeClass("disabled");
				window
					.jQuery(
						".vs-dialog-cancel-button",
						this.$refs.funcionalidadeURLDialogo.$refs.dialogx
					)
					.attr("disabled", null);
			} else {
				window
					.jQuery(
						".vs-dialog-cancel",
						this.$refs.funcionalidadeURLDialogo.$refs.dialogx
					)
					.addClass("disabled");
				window
					.jQuery(
						".vs-dialog-dark",
						this.$refs.funcionalidadeURLDialogo.$refs.con
					)
					.addClass("disabled");
				window
					.jQuery(
						".vs-dialog-cancel-button",
						this.$refs.funcionalidadeURLDialogo.$refs.dialogx
					)
					.attr("disabled", "disabled");
			}
		},
		mudarFuncionalidadeIconeDialogoStatus(status) {
			if (status) {
				window
					.jQuery(
						".vs-dialog-cancel",
						this.$refs.funcionalidadeIconeDialogo.$refs.dialogx
					)
					.removeClass("disabled");
				window
					.jQuery(
						".vs-dialog-dark",
						this.$refs.funcionalidadeIconeDialogo.$refs.con
					)
					.removeClass("disabled");
				window
					.jQuery(
						".vs-dialog-cancel-button",
						this.$refs.funcionalidadeIconeDialogo.$refs.dialogx
					)
					.attr("disabled", null);
			} else {
				window
					.jQuery(
						".vs-dialog-cancel",
						this.$refs.funcionalidadeIconeDialogo.$refs.dialogx
					)
					.addClass("disabled");
				window
					.jQuery(
						".vs-dialog-dark",
						this.$refs.funcionalidadeIconeDialogo.$refs.con
					)
					.addClass("disabled");
				window
					.jQuery(
						".vs-dialog-cancel-button",
						this.$refs.funcionalidadeIconeDialogo.$refs.dialogx
					)
					.attr("disabled", "disabled");
			}
		},
		mudarFuncionalidadeRemoverDialogoStatus(status) {
			if (status) {
				window
					.jQuery(
						".vs-dialog-cancel",
						this.$refs.funcionalidadeRemoverDialogo.$refs.dialogx
					)
					.removeClass("disabled");
				window
					.jQuery(
						".vs-dialog-dark",
						this.$refs.funcionalidadeRemoverDialogo.$refs.con
					)
					.removeClass("disabled");
				window
					.jQuery(
						".vs-dialog-cancel-button",
						this.$refs.funcionalidadeRemoverDialogo.$refs.dialogx
					)
					.attr("disabled", null);
			} else {
				window
					.jQuery(
						".vs-dialog-cancel",
						this.$refs.funcionalidadeRemoverDialogo.$refs.dialogx
					)
					.addClass("disabled");
				window
					.jQuery(
						".vs-dialog-dark",
						this.$refs.funcionalidadeRemoverDialogo.$refs.con
					)
					.addClass("disabled");
				window
					.jQuery(
						".vs-dialog-cancel-button",
						this.$refs.funcionalidadeRemoverDialogo.$refs.dialogx
					)
					.attr("disabled", "disabled");
			}
		},
		mudarFuncionalidadeAdicionarDialogoStatus(status) {
			if (status) {
				window
					.jQuery(
						".vs-dialog-cancel",
						this.$refs.funcionalidadeAdicionarDialogo.$refs.dialogx
					)
					.removeClass("disabled");
				window
					.jQuery(
						".vs-dialog-dark",
						this.$refs.funcionalidadeAdicionarDialogo.$refs.con
					)
					.removeClass("disabled");
				window
					.jQuery(
						".vs-dialog-cancel-button",
						this.$refs.funcionalidadeAdicionarDialogo.$refs.dialogx
					)
					.attr("disabled", null);
			} else {
				window
					.jQuery(
						".vs-dialog-cancel",
						this.$refs.funcionalidadeAdicionarDialogo.$refs.dialogx
					)
					.addClass("disabled");
				window
					.jQuery(
						".vs-dialog-dark",
						this.$refs.funcionalidadeAdicionarDialogo.$refs.con
					)
					.addClass("disabled");
				window
					.jQuery(
						".vs-dialog-cancel-button",
						this.$refs.funcionalidadeAdicionarDialogo.$refs.dialogx
					)
					.attr("disabled", "disabled");
			}
		},
		processarFuncionalidadeRenomear() {
			this.funcionalidadeRenomearDialogoStatus = true;
			if (
				this.backendOcupado ||
				!this.camposEditar.id ||
				!this.registroSelecionado
			) {
				return;
			}
			this.backendOcupado = true;
			this.mensagemErro = "";
			this.mudarFuncionalidadeRenomearDialogoStatus(false);
			this.$validator.validate("formularioRenomear.*").then((result) => {
					if (result) {
						axios.patch(this.backendUrl + `/${this.camposEditar.id}/renomear`, this.camposFormularioRenomear)
							.then((response) => {
								if (
									response &&
									response.status == 200 &&
									response.data &&
									response.data.status == "atualizado"
								) {
									this.$vs.notify({
										title: "Sucesso",
										text: `${this.backendModel} renomeada.`,
										iconPack: "feather",
										icon: "icon-check",
										color: "success",
									});
									this.backendOcupado = false;
									this.mudarFuncionalidadeRenomearDialogoStatus(
										true
									);
									this.funcionalidadeRenomearDialogoStatus = false;
									this.registroSelecionado.nome =
										this.camposFormularioRenomear.nome;
									this.limparCamposFormularioRenomear();
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
									this.mudarFuncionalidadeRenomearDialogoStatus(
										true
									);
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
										error.response.data.errors instanceof
											Object
									) {
										var erros = {};
										Object.keys(
											this.camposFormularioRenomear
										).forEach((key) => {
											if (
												error.response.data.errors.hasOwnProperty(
													key
												) &&
												error.response.data.errors[
													key
												] &&
												error.response.data.errors[key]
													.length > 0 &&
												error.response.data.errors[
													key
												][0]
											) {
												erros[key] = true;
												this.errors.add({
													field: key,
													scope: "formularioRenomear",
													msg:
														error.response.data
															.errors[key][0],
												});
											}
										});
										Object.keys(
											error.response.data.errors
										).forEach((key) => {
											if (
												error.response.data.errors[
													key
												] &&
												error.response.data.errors[key]
													.length > 0 &&
												error.response.data.errors[
													key
												][0] &&
												(!erros ||
													!erros.hasOwnProperty(
														key
													) ||
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
								this.mudarFuncionalidadeRenomearDialogoStatus(
									true
								);
							});
					} else {
						this.backendOcupado = false;
						this.mudarFuncionalidadeRenomearDialogoStatus(true);
					}
				});
		},
		processarFuncionalidadeURL() {
			this.funcionalidadeURLDialogoStatus = true;
			if (
				this.backendOcupado ||
				!this.camposEditar.id ||
				!this.registroSelecionado
			) {
				return;
			}
			this.backendOcupado = true;
			this.mensagemErro = "";
			this.mudarFuncionalidadeURLDialogoStatus(false);
			this.$validator.validate("formularioURL.*").then((result) => {
				if (result) {
					axios.patch(this.backendUrl + `/${this.camposEditar.id}/url`, this.camposFormularioURL)
						.then((response) => {
							if (
								response &&
								response.status == 200 &&
								response.data &&
								response.data.status == "atualizado"
							) {
								this.$vs.notify({
									title: "Sucesso",
									text: `${this.backendModel} URL alterada.`,
									iconPack: "feather",
									icon: "icon-check",
									color: "success",
								});
								this.backendOcupado = false;
								this.mudarFuncionalidadeURLDialogoStatus(true);
								this.funcionalidadeURLDialogoStatus = false;
								this.registroSelecionado.url =
									this.camposFormularioURL.url;
								this.limparCamposFormularioURL();
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
								this.mudarFuncionalidadeURLDialogoStatus(true);
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
										this.camposFormularioURL
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
												scope: "formularioURL",
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
							this.mudarFuncionalidadeURLDialogoStatus(true);
						});
				} else {
					this.backendOcupado = false;
					this.mudarFuncionalidadeURLDialogoStatus(true);
				}
			});
		},
		processarFuncionalidadeIcone() {
			this.funcionalidadeIconeDialogoStatus = true;
			if (
				this.backendOcupado ||
				!this.camposEditar.id ||
				!this.registroSelecionado
			) {
				return;
			}
			this.backendOcupado = true;
			this.mensagemErro = "";
			this.mudarFuncionalidadeIconeDialogoStatus(false);
			this.$validator.validate("formularioIcone.*").then((result) => {
					if (result) {
						axios.patch(this.backendUrl + `/${this.camposEditar.id}/icone`, this.camposFormularioIcone)
							.then((response) => {
								if (
									response &&
									response.status == 200 &&
									response.data &&
									response.data.status == "atualizado"
								) {
									this.$vs.notify({
										title: "Sucesso",
										text: `${this.backendModel} ícone alterado.`,
										iconPack: "feather",
										icon: "icon-check",
										color: "success",
									});
									this.backendOcupado = false;
									this.mudarFuncionalidadeIconeDialogoStatus(
										true
									);
									this.funcionalidadeIconeDialogoStatus = false;
									this.registroSelecionado.icon =
										this.camposFormularioIcone.icone;
									this.limparCamposFormularioIcone();
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
									this.mudarFuncionalidadeIconeDialogoStatus(
										true
									);
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
										error.response.data.errors instanceof
											Object
									) {
										var erros = {};
										Object.keys(
											this.camposFormularioIcone
										).forEach((key) => {
											if (
												error.response.data.errors.hasOwnProperty(
													key
												) &&
												error.response.data.errors[
													key
												] &&
												error.response.data.errors[key]
													.length > 0 &&
												error.response.data.errors[
													key
												][0]
											) {
												erros[key] = true;
												this.errors.add({
													field: key,
													scope: "formularioIcone",
													msg:
														error.response.data
															.errors[key][0],
												});
											}
										});
										Object.keys(
											error.response.data.errors
										).forEach((key) => {
											if (
												error.response.data.errors[
													key
												] &&
												error.response.data.errors[key]
													.length > 0 &&
												error.response.data.errors[
													key
												][0] &&
												(!erros ||
													!erros.hasOwnProperty(
														key
													) ||
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
								this.mudarFuncionalidadeIconeDialogoStatus(
									true
								);
							});
					} else {
						this.backendOcupado = false;
						this.mudarFuncionalidadeIconeDialogoStatus(true);
					}
				});
		},
		processarFuncionalidadeRemover() {
			this.funcionalidadeRemoverDialogoStatus = true;
			if (
				this.backendOcupado ||
				!this.camposEditar.id ||
				!this.registroSelecionado ||
				!this.funcionalidades ||
				!this.funcionalidades.length
			) {
				return;
			}
			this.backendOcupado = true;
			this.mensagemErro = "";
			this.mudarFuncionalidadeRemoverDialogoStatus(false);
			axios.delete(this.backendUrl + `/${this.camposEditar.id}`)
				.then((response) => {
					if (
						response &&
						response.status == 200 &&
						response.data &&
						response.data.status == "removido"
					) {
						if (
							this.funcionalidades &&
							this.funcionalidades.length > 0 &&
							this.registroSelecionado &&
							this.registroSelecionado.hasOwnProperty("id") &&
							this.registroSelecionado.id
						) {
							var parente;
							for (var i in this.funcionalidades) {
								if (this.funcionalidades[i]) {
									parente = this.localizarItem(
										this.funcionalidades[i],
										this.registroSelecionado.id
									);
									if (parente) {
										break;
									}
								}
							}
							if (
								parente &&
								parente.hasOwnProperty("children") &&
								parente.children &&
								parente.children.length > 0
							) {
								var index = parente.children.findIndex(
									(t) => t.id === this.registroSelecionado.id
								);
								if (index >= 0) {
									parente.children.splice(index, 1);
								}
							}
						}
						this.$vs.notify({
							title: "Sucesso",
							text: `${this.backendModel} removida.`,
							iconPack: "feather",
							icon: "icon-check",
							color: "success",
						});
						this.backendOcupado = false;
						this.mudarFuncionalidadeRemoverDialogoStatus(true);
						this.funcionalidadeRemoverDialogoStatus = false;
						this.limparCamposFormularioRemover();
					} else {
						this.$vs.notify({
							title: "Erro",
							text: "Dados de resposta do registro inválidos.",
							iconPack: "feather",
							icon: "icon-alert-circle",
							color: "danger",
						});
						this.backendOcupado = false;
						this.mudarFuncionalidadeRemoverDialogoStatus(true);
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
					this.backendOcupado = false;
					this.mudarFuncionalidadeRemoverDialogoStatus(true);
				});
		},
		processarFuncionalidadeAdicionar() {
			this.funcionalidadeAdicionarDialogoStatus = true;
			if (
				this.backendOcupado ||
				!this.camposAdicionar.nome ||
				!this.registroSelecionado
			) {
				return;
			}
			this.backendOcupado = true;
			this.mensagemErro = "";
			this.mudarFuncionalidadeAdicionarDialogoStatus(false);
			this.$validator.validate("formularioAdicionar.*").then((result) => {
					if (result) {
						axios.post(this.backendUrl, this.camposFormularioAdicionar)
							.then((response) => {
								if (
									response &&
									response.status == 201 &&
									response.data &&
									response.data.status == "criado" &&
									this.registroSelecionado &&
									this.registroSelecionado.hasOwnProperty(
										"id"
									) &&
									this.registroSelecionado.id &&
									(this.registroSelecionado.codigo ||
										this.registroSelecionado.codigo == -1)
								) {
									this.registroSelecionado.addChild({
										codigo: response.data.id,
										nome:
											this.camposFormularioAdicionar.nome,
										url: this.camposFormularioAdicionar.url,
										icon: this.camposFormularioAdicionar
											.icone
											? this.camposFormularioAdicionar
													.icone
											: !this.camposFormularioAdicionar
													.url
											? "fa fa-folder"
											: "fa fa-desktop",
										children: [],
									});
									this.$vs.notify({
										title: "Sucesso",
										text: `${this.backendModel} criada.`,
										iconPack: "feather",
										icon: "icon-check",
										color: "success",
									});
									this.backendOcupado = false;
									this.mudarFuncionalidadeAdicionarDialogoStatus(
										true
									);
									this.funcionalidadeAdicionarDialogoStatus = false;
									this.registroSelecionado = null;
									this.limparCamposFormularioAdicionar();
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
									this.mudarFuncionalidadeAdicionarDialogoStatus(
										true
									);
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
										error.response.data.errors instanceof
											Object
									) {
										var erros = {};
										Object.keys(
											this.camposFormularioAdicionar
										).forEach((key) => {
											if (
												error.response.data.errors.hasOwnProperty(
													key
												) &&
												error.response.data.errors[
													key
												] &&
												error.response.data.errors[key]
													.length > 0 &&
												error.response.data.errors[
													key
												][0]
											) {
												erros[key] = true;
												this.errors.add({
													field: key,
													scope:
														"formularioAdicionar",
													msg:
														error.response.data
															.errors[key][0],
												});
											}
										});
										Object.keys(
											error.response.data.errors
										).forEach((key) => {
											if (
												error.response.data.errors[
													key
												] &&
												error.response.data.errors[key]
													.length > 0 &&
												error.response.data.errors[
													key
												][0] &&
												(!erros ||
													!erros.hasOwnProperty(
														key
													) ||
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
								this.mudarFuncionalidadeAdicionarDialogoStatus(
									true
								);
							});
					} else {
						this.backendOcupado = false;
						this.mudarFuncionalidadeAdicionarDialogoStatus(true);
					}
				});
		},
		onItemDoubleClick(item) {
			if (
				item &&
				item.hasOwnProperty("children") &&
				item.children &&
				item.children.length > 0
			) {
				if (item.opened) {
					item.opened = false;
				} else {
					item.opened = true;
				}
			}
		},
		onItemClick(node, item, event) {
			//console.log(node.model.nome + " clicked !", item);
		},
		onItemToggle(node, item, event) {
			//console.log(node.model.nome + " onItemToggle !", item);
		},
		onItemDragStart(node, item, event) {
			//console.log("DRAG START", node, item);
		},
		onItemDragEnd(node, item, event) {
			//console.log("DRAG END", node, item);
		},
		onItemDropBefore(node, item, draggedItem, event) {
			//console.log("DROP BEFORE", node, item, draggedItem);
		},
		onItemDrop(node, targetItem, draggedItem, event) {
			/*console.log(
				"DROP",
				targetItem.nome,
				draggedItem.index,
				draggedItem.item.nome
			);*/
		},
		onItemDropSiblingLeft(node, targetItem, draggedItem, index, event) {
			/*console.log(
				"DROP LEFT",
				targetItem.nome,
				index,
				draggedItem.index,
				draggedItem.item.nome
			);*/
		},
		onItemDropSiblingRight(node, targetItem, draggedItem, index, event) {
			/*console.log(
				"DROP RIGHT",
				targetItem.nome,
				index,
				draggedItem.index,
				draggedItem.item.nome
			);*/
		},
		onMenuContextoFechar() {
			//console.log("The context menu was closed");
		},
		onMenuContextoAbrir(event, item, top, left) {
			if (this.funcionalidades && this.funcionalidades.length > 0) {
				this.funcionalidades.forEach((funcionalidade) => {
					if (funcionalidade) {
						this.desmarcarFuncionalidade(funcionalidade);
					}
				});
			}
			if (item) {
				item.selected = true;
			}
		},
		funcionalidadeRenomear(item) {
			if (
				this.backendOcupado ||
				this.funcionalidadeRenomearDialogoStatus ||
				!item ||
				!item.hasOwnProperty("codigo") ||
				!item.hasOwnProperty("nome") ||
				!item.codigo ||
				!item.nome
			) {
				return;
			}
			if (this.funcionalidades && this.funcionalidades.length > 0) {
				this.funcionalidades.forEach((funcionalidade) => {
					if (funcionalidade) {
						this.desmarcarFuncionalidade(funcionalidade);
					}
				});
			}
			item.selected = true;
			this.mensagemErro = "";
			this.camposEditar.id = item.codigo;
			this.camposEditar.nome = item.nome;
			this.registroSelecionado = item;
			this.funcionalidadeRenomearDialogoStatus = true;
			this.$nextTick(() => {
				setTimeout(() => {
					window
						.jQuery(
							this.$refs.funcionalidadeRenomearDialogo
								.$children[0].$children[0].$refs.vsinput
						)
						.trigger("focus");
				}, 150);
			});
		},
		funcionalidadeURL(item) {
			if (
				this.backendOcupado ||
				this.funcionalidadeURLDialogoStatus ||
				!item ||
				!item.hasOwnProperty("codigo") ||
				!item.hasOwnProperty("url") ||
				!item.codigo
			) {
				return;
			}
			if (this.funcionalidades && this.funcionalidades.length > 0) {
				this.funcionalidades.forEach((funcionalidade) => {
					if (funcionalidade) {
						this.desmarcarFuncionalidade(funcionalidade);
					}
				});
			}
			item.selected = true;
			this.mensagemErro = "";
			this.camposEditar.id = item.codigo;
			this.camposEditar.url = item.url;
			this.registroSelecionado = item;
			this.funcionalidadeURLDialogoStatus = true;
			this.$nextTick(() => {
				setTimeout(() => {
					window
						.jQuery(
							this.$refs.funcionalidadeURLDialogo.$children[0]
								.$children[0].$refs.vsinput
						)
						.trigger("focus");
				}, 150);
			});
		},
		funcionalidadeIcone(item) {
			if (
				this.backendOcupado ||
				this.funcionalidadeIconeDialogoStatus ||
				!item ||
				!item.hasOwnProperty("codigo") ||
				!item.hasOwnProperty("icon") ||
				!item.codigo
			) {
				return;
			}
			if (this.funcionalidades && this.funcionalidades.length > 0) {
				this.funcionalidades.forEach((funcionalidade) => {
					if (funcionalidade) {
						this.desmarcarFuncionalidade(funcionalidade);
					}
				});
			}
			item.selected = true;
			this.mensagemErro = "";
			this.camposEditar.id = item.codigo;
			this.camposEditar.icone = item.icon;
			this.registroSelecionado = item;
			this.funcionalidadeIconeDialogoStatus = true;
			this.$nextTick(() => {
				setTimeout(() => {
					window
						.jQuery(
							this.$refs.funcionalidadeIconeDialogo.$children[0]
								.$children[0].$refs.vsinput
						)
						.trigger("focus");
				}, 150);
			});
		},
		funcionalidadeAdicionar(item) {
			if (
				this.backendOcupado ||
				this.funcionalidadeAdicionarDialogoStatus ||
				!item ||
				!item.hasOwnProperty("codigo") ||
				!item.hasOwnProperty("id") ||
				!item.codigo ||
				!item.id
			) {
				return;
			}
			if (this.funcionalidades && this.funcionalidades.length > 0) {
				this.funcionalidades.forEach((funcionalidade) => {
					if (funcionalidade) {
						this.desmarcarFuncionalidade(funcionalidade);
					}
				});
			}
			item.selected = true;
			this.mensagemErro = "";
			if (item.codigo > 0) {
				this.camposAdicionar.idParente = item.codigo;
			} else {
				this.camposAdicionar.idParente = null;
			}
			this.registroSelecionado = item;
			this.funcionalidadeAdicionarDialogoStatus = true;
			this.$nextTick(() => {
				setTimeout(() => {
					window
						.jQuery(
							this.$refs.funcionalidadeAdicionarDialogo
								.$children[0].$children[0].$refs.vsinput
						)
						.trigger("focus");
				}, 150);
			});
		},
		funcionalidadeRemover(item) {
			if (
				this.backendOcupado ||
				!item ||
				!item.hasOwnProperty("codigo") ||
				!item.hasOwnProperty("id") ||
				!item.codigo ||
				!item.id ||
				!this.funcionalidades ||
				!this.funcionalidades.length
			) {
				return;
			}
			this.funcionalidades.forEach((funcionalidade) => {
				if (funcionalidade) {
					this.desmarcarFuncionalidade(funcionalidade);
				}
			});
			item.selected = true;
			this.mensagemErro = "";
			this.camposEditar.id = item.codigo;
			this.registroSelecionado = item;
			this.funcionalidadeRemoverDialogoStatus = true;
			this.$nextTick(() => {
				setTimeout(() => {
					window
						.jQuery(
							".vs-dialog-cancel-button",
							this.$refs.funcionalidadeRemoverDialogo.$refs
								.dialogx
						)
						.trigger("focus");
				}, 150);
			});
		},
	},
	mounted() {
		this.funcionalidades = [this.$refs.menuList.initializeLoading()];
		this.$refs.menuList.handleAsyncLoad(
			this.funcionalidades,
			this.$refs.menuList
		);
		this.backendOcupado = true;
		axios.get(this.backendUrl)
			.then((response) => {
				if (
					response &&
					response.status == 200 &&
					response.data &&
					response.data instanceof Object
				) {
					if (
						response.data.hasOwnProperty("funcionalidades") &&
						response.data.funcionalidades &&
						response.data.funcionalidades instanceof Array
					) {
						this.funcionalidades = response.data.funcionalidades;
						this.$refs.menuList.initializeData(
							this.funcionalidades
						);
					} else {
						this.$vs.notify({
							title: "Erro",
							text:
								"Incapaz de carregar a lista de funcionalidades.",
							iconPack: "feather",
							icon: "icon-alert-circle",
							color: "danger",
						});
						this.funcionalidades = [];
					}
				} else {
					this.$vs.notify({
						title: "Erro",
						text: "Incapaz de carregar a lista de funcionalidades.",
						iconPack: "feather",
						icon: "icon-alert-circle",
						color: "danger",
					});
					this.funcionalidades = [];
				}
				this.backendOcupado = false;
			})
			.catch((error) => {
				var errMsg = "Erro na requisição.";
				this.funcionalidades = [];
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
.paginaSegurancaFuncionalidade {
	div.tree svg.svg-inline--fa.tree-icon {
		width: 20px;
		height: 20px;
		margin-right: 5px;
	}
	div.tree i.tree-icon.icon-root {
		background-image: url(/images/logo/accordous-black.png);
		background-size: 24px 20px;
		background-position-y: 2px;
		background-position-x: 1px;
	}
	ul.v-context li a[role="menuitem"] {
		cursor: pointer;
	}
	ul.v-context li div.vs-component.vs-divider {
		margin: 0px;
	}
	ul.v-context li a:focus,
	ul.v-context li a:hover {
		background-color: initial !important;
	}
	/*ul.v-context li a.menuItemRemover:focus,
	ul.v-context li a.menuItemRemover:hover {
		background-color: rgba(var(--vs-danger), 1) !important;
		color: #ffffff !important;
	}*/
}
.paginaSegurancaFuncionalidade.con-vs-dialog.editar-dialogo
	.vs-dialog-dark.disabled {
	pointer-events: none !important;
}
.paginaSegurancaFuncionalidade.con-vs-dialog.editar-dialogo .vs-dialog {
	max-width: 530px;
}
.paginaSegurancaFuncionalidade.con-vs-dialog.editar-dialogo
	.vs-dialog
	header
	.vs-dialog-cancel.disabled {
	pointer-events: none !important;
}
[dir]
	.paginaSegurancaFuncionalidade.con-vs-dialog.editar-dialogo
	.vs-dialog
	.vs-dialog-text {
	padding: 0;
}
.paginaSegurancaFuncionalidade.con-vs-dialog.editar-dialogo
	.vs-dialog
	.vs-dialog-text
	.scroll-area {
	max-height: 75vh;
}
.paginaSegurancaFuncionalidade.con-vs-dialog.editar-dialogo .vs-dialog footer {
	display: flow-root;
}
.paginaSegurancaFuncionalidade.con-vs-dialog.editar-dialogo
	.vs-dialog
	footer
	button.vs-dialog-accept-button,
.paginaSegurancaFuncionalidade.con-vs-dialog.editar-dialogo
	.vs-dialog
	footer
	button.vs-dialog-cancel-button {
	float: right;
}
.paginaSegurancaFuncionalidade.con-vs-dialog.editar-dialogo
	.vs-dialog
	footer
	button.vs-dialog-accept-button {
	margin-left: 0.5rem !important;
}
.paginaSegurancaFuncionalidade.con-vs-dialog.editar-dialogo
	.vs-dialog
	footer
	button.vs-dialog-cancel-button {
	//background: rgba(var(--vs-danger), 1) !important;
	color: rgba(var(--vs-danger), 1) !important;
}
.paginaSegurancaFuncionalidade.con-vs-dialog.editar-dialogo
	.vs-dialog
	footer
	button.vs-dialog-cancel-button:hover {
	background: rgba(var(--vs-danger), 0.08) !important;
}
.paginaSegurancaFuncionalidade.con-vs-dialog.editar-dialogo
	div.vs-input.campoRequerido
	> label,
.paginaSegurancaFuncionalidade.con-vs-dialog.editar-dialogo
	div.vs-input.campoRequerido
	> div.vs-con-input
	> span.vs-placeholder-label,
.paginaSegurancaFuncionalidade.con-vs-dialog.editar-dialogo
	label.campoRequerido {
	font-weight: 500;
}
.paginaSegurancaFuncionalidade.con-vs-dialog.editar-dialogo
	div.vs-input.campoRequerido
	> label::after,
.paginaSegurancaFuncionalidade.con-vs-dialog.editar-dialogo
	div.vs-input.campoRequerido
	> div.vs-con-input
	> span.vs-placeholder-label::after,
.paginaSegurancaFuncionalidade.con-vs-dialog.editar-dialogo
	label.campoRequerido::after {
	content: " *";
	color: red;
}
.paginaSegurancaFuncionalidade.con-vs-dialog.editar-dialogo
	.vs-select--label:not(.input-select-label-danger--active):not(.input-select-label-success--active) {
	color: rgba(0, 0, 0, 0.7);
}
</style>
