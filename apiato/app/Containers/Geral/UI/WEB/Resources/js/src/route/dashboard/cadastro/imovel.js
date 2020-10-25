let titulo = 'Imóvel';
let url = 'imovel';
let icone = 'fa fa-home';

module.exports = [
	{
		path: '/cadastro/' + url,
		name: 'cadastro-' + url,
		component: () => import('@/views/apps/cadastro/Imovel/Index.vue'),
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Cadastro'
				},
				{
					title: titulo,
					icon: icone,
					active: true
				},
			],
			pageTitle: titulo,
			rule: 'admin',
		}
	}, {
		path: '/cadastro/' + url + '/criar',
		name: 'cadastro-' + url + '-criar',
		component: () => import('@/views/apps/cadastro/Imovel/CriarRegistro.vue'),
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Cadastro'
				},
				{
					title: titulo,
					icon: icone,
					url: '/cadastro/' + url
				},
				{
					title: 'Criar',
					active: true
				},
			],
			pageTitle: titulo + ' - Criar',
			rule: 'admin',
			parent: 'cadastro-' + url
		}
	}, {
		path: '/cadastro/' + url + '/editar/:id',
		name: 'cadastro-' + url + '-editar',
		component: () => import('@/views/apps/cadastro/Imovel/EditarRegistro.vue'),
		props: true,
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Cadastro'
				},
				{
					title: titulo,
					icon: icone,
					url: '/cadastro/' + url
				},
				{
					title: 'Alterar',
					active: true
				}
			],
			pageTitle: titulo + ' - Alterar',
			rule: 'admin',
			parent: 'cadastro-' + url
		}
	}, {
		path: '/cadastro/' + url + '/visualizar/:id',
		name: 'cadastro-' + url + '-visualizar',
		component: () => import('@/views/apps/cadastro/Imovel/VisualizarRegistro.vue'),
		props: true,
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Cadastro'
				},
				{
					title: titulo,
					icon: icone,
					url: '/cadastro/' + url
				},
				{
					title: 'Visualizar',
					active: true
				}
			],
			pageTitle: titulo + ' - Visualizar',
			rule: 'admin',
			parent: 'cadastro-' + url
		}
	}, {
		path: '/cadastro/' + url + '/historico/:id',
		name: 'cadastro-' + url + '-historico',
		component: () => import('@/views/apps/cadastro/Imovel/Historico/Index.vue'),
		props: true,
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Cadastro'
				},
				{
					title: titulo,
					icon: icone,
					url: '/cadastro/' + url
				},
				{
					title: 'Histórico',
					active: true
				},
			],
			pageTitle: titulo + ' - Histórico',
			rule: 'historicoAlteracao',
			parent: 'cadastro-' + url
		}
	}, {
		path: '/cadastro/' + url + '/historico/:id/:idHistorico',
		name: 'cadastro-' + url + '-historico-visualizar',
		component: () => import('@/views/apps/cadastro/Imovel/Historico/VisualizarRegistro.vue'),
		props: true,
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Cadastro'
				},
				{
					title: titulo,
					icon: icone,
					url: '/cadastro/' + url
				},
				{
					title: 'Histórico',
					url: '/cadastro/' + url + '/historico/:id'
				},
				{
					title: 'Visualizar',
					active: true
				},
			],
			pageTitle: titulo + ' - Histórico - Visualizar',
			rule: 'historicoAlteracao',
			parent: 'cadastro-' + url
		}
	}, {
		path: '/cadastro/' + url + '/removido',
		name: 'cadastro-' + url + '-removido',
		component: () => import('@/views/apps/cadastro/Imovel/Removido/Index.vue'),
		props: true,
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Cadastro'
				},
				{
					title: titulo,
					icon: icone,
					url: '/cadastro/' + url
				},
				{
					title: 'Removido',
					active: true
				},
			],
			pageTitle: titulo + ' - Removido',
			rule: 'historicoAlteracao',
			parent: 'cadastro-' + url
		}
	}, {
		path: '/cadastro/' + url + '/removido/historico/:id',
		name: 'cadastro-' + url + '-removido-historico',
		component: () => import('@/views/apps/cadastro/Imovel/HistoricoRemovido/Index.vue'),
		props: true,
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Cadastro'
				},
				{
					title: titulo,
					icon: icone,
					url: '/cadastro/' + url
				},
				{
					title: 'Removido',
					url: '/cadastro/' + url + '/removido'
				},
				{
					title: 'Histórico',
					active: true
				},
			],
			pageTitle: titulo + ' - Removido - Histórico',
			rule: 'historicoAlteracao',
			parent: 'cadastro-' + url
		}
	}, {
		path: '/cadastro/' + url + '/removido/historico/:id/:idHistorico',
		name: 'cadastro-' + url + '-removido-historico-visualizar',
		component: () => import('@/views/apps/cadastro/Imovel/HistoricoRemovido/VisualizarRegistro.vue'),
		props: true,
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Cadastro'
				},
				{
					title: titulo,
					icon: icone,
					url: '/cadastro/' + url
				},
				{
					title: 'Removido',
					url: '/cadastro/' + url + '/removido'
				},
				{
					title: 'Histórico',
					url: '/cadastro/' + url + '/removido/historico/:id'
				},
				{
					title: 'Visualizar',
					active: true
				},
			],
			pageTitle: titulo + ' - Removido - Histórico - Visualizar',
			rule: 'historicoAlteracao',
			parent: 'cadastro-' + url
		}
	}
];
