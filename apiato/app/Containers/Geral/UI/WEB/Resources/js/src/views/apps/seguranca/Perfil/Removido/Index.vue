<template>
	<div :class="configuracaoPagina.nomeEstilo + 'Removido'">
		<lista-registros
			ref="listaRegistros"
			:backendUrl="backendUrl"
			:backendModel="backendModel"
			:dialogoHistoricoRegistroAbrir="historicoRegistroAbrir"
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
				"/removido"
			);
		},
		backendModel() {
			return this.configuracaoPagina.backendModel;
		},
	},
	methods: {
		historicoRegistroAbrir(record) {
			if (this.backendOcupado) {
				return;
			}
			this.backendOcupado = true;
			this.$router.push({
				name:
					this.configuracaoPagina.routerName + "-removido-historico",
				params: {
					id: record.id,
				},
			});
		},
		listaRegistrosAtualizar() {
			this.$refs.listaRegistros.atualizarListaRegistros();
		},
	},
};
</script>

