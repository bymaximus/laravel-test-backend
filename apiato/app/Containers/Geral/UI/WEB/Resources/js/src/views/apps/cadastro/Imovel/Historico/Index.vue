<template>
	<div :class="configuracaoPagina.nomeEstilo + 'Historico'">
		<lista-registros
			ref="listaRegistros"
			:backendUrl="backendUrl"
			:backendModel="backendModel"
			:dialogoEditarRegistroAbrir="editarRegistroAbrir"
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
			configuracaoPagina: require("./../configs.js"),
			backendOcupado: false,
		};
	},
	computed: {
		backendUrl() {
			return (
				this.$urlBasePath +
				this.configuracaoPagina.backendUrl +
				`/${this.$route.params.id}/historico`
			);
		},
		backendModel() {
			return this.configuracaoPagina.backendModel;
		},
	},
	methods: {
		editarRegistroAbrir(record) {
			if (this.backendOcupado) {
				return;
			}
			this.backendOcupado = true;
			this.$router.push({
				name:
					this.configuracaoPagina.routerName +
					"-historico-visualizar",
				params: {
					id: this.$route.params.id,
					idHistorico: record.id,
				},
			});
		},
		listaRegistrosAtualizar() {
			this.$refs.listaRegistros.atualizarListaRegistros();
		},
	},
};
</script>

