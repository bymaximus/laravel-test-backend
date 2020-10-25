<template>
	<div :class="configuracaoPagina.nomeEstilo">
		<lista-registros
			ref="listaRegistros"
			:backendUrl="backendUrl"
			:backendModel="backendModel"
			:dialogoCriarRegistroAbrir="criarRegistroAbrir"
			:dialogoEditarRegistroAbrir="editarRegistroAbrir"
			:dialogoHistoricoRegistroAbrir="historicoRegistroAbrir"
			:dialogoHistoricoRegistroRemovidoAbrir="historicoRegistroRemovidoAbrir"
		></lista-registros>
	</div>
</template>

<script>
import Vue from "vue";
import { BootstrapVue } from "bootstrap-vue";

Vue.use(BootstrapVue);

import ListaRegistros from "./ListaRegistros.vue";

export default {
	components: {
		ListaRegistros,
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
			backendOcupado: false,
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
		criarRegistroAbrir() {
			if (this.backendOcupado) {
				return;
			}
			this.backendOcupado = true;
			this.$router.push(this.configuracaoPagina.routerPath + "/criar");
		},
		editarRegistroAbrir(record) {
			if (this.backendOcupado) {
				return;
			}
			this.backendOcupado = true;
			this.$router.push({
				name: this.configuracaoPagina.routerName + "-editar",
				params: {
					id: record.id,
				},
			});
		},
		historicoRegistroAbrir(record) {
			if (this.backendOcupado) {
				return;
			}
			this.backendOcupado = true;
			this.$router.push({
				name: this.configuracaoPagina.routerName + "-historico",
				params: {
					id: record.id,
				},
			});
		},
		historicoRegistroRemovidoAbrir(record) {
			if (this.backendOcupado) {
				return;
			}
			this.backendOcupado = true;
			this.$router.push(this.configuracaoPagina.routerPath + "/removido");
		},
		listaRegistrosAtualizar() {
			this.$refs.listaRegistros.atualizarListaRegistros();
		},
	},
};
</script>

