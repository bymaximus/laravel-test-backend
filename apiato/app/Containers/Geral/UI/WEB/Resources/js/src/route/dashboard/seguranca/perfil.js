let titulo = 'Perfil';
let url = 'perfil';
let icone = 'fa fa-id-card';

module.exports = [
	{
		path: '/seguranca/' + url,
		name: 'seguranca-' + url,
		component: () => import('@/views/apps/seguranca/Perfil/Index.vue'),
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Segurança'
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
		path: '/seguranca/' + url + '/criar',
		name: 'seguranca-' + url + '-criar',
		component: () => import('@/views/apps/seguranca/Perfil/CriarRegistro.vue'),
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Segurança'
				},
				{
					title: titulo,
					icon: icone,
					url: '/seguranca/' + url
				},
				{
					title: 'Criar',
					active: true
				},
			],
			pageTitle: titulo + ' - Criar',
			rule: 'admin',
			parent: 'seguranca-' + url
		}
	}, {
		path: '/seguranca/' + url + '/editar/:id',
		name: 'seguranca-' + url + '-editar',
		component: () => import('@/views/apps/seguranca/Perfil/EditarRegistro.vue'),
		props: true,
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Segurança'
				},
				{
					title: titulo,
					icon: icone,
					url: '/seguranca/' + url
				},
				{
					title: 'Alterar',
					active: true
				}
			],
			pageTitle: titulo + ' - Alterar',
			rule: 'admin',
			parent: 'seguranca-' + url
		}
	}, {
		path: '/seguranca/' + url + '/historico/:id',
		name: 'seguranca-' + url + '-historico',
		component: () => import('@/views/apps/seguranca/Perfil/Historico/Index.vue'),
		props: true,
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Segurança'
				},
				{
					title: titulo,
					icon: icone,
					url: '/seguranca/' + url
				},
				{
					title: 'Histórico',
					active: true
				},
			],
			pageTitle: titulo + ' - Histórico',
			rule: 'historicoAlteracao',
			parent: 'seguranca-' + url
		}
	}, {
		path: '/seguranca/' + url + '/historico/:id/:idHistorico',
		name: 'seguranca-' + url + '-historico-visualizar',
		component: () => import('@/views/apps/seguranca/Perfil/Historico/VisualizarRegistro.vue'),
		props: true,
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Segurança'
				},
				{
					title: titulo,
					icon: icone,
					url: '/seguranca/' + url
				},
				{
					title: 'Histórico',
					url: '/seguranca/' + url + '/historico/:id'
				},
				{
					title: 'Visualizar',
					active: true
				},
			],
			pageTitle: titulo + ' - Histórico - Visualizar',
			rule: 'historicoAlteracao',
			parent: 'seguranca-' + url
		}
	}, {
		path: '/seguranca/' + url + '/removido',
		name: 'seguranca-' + url + '-removido',
		component: () => import('@/views/apps/seguranca/Perfil/Removido/Index.vue'),
		props: true,
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Segurança'
				},
				{
					title: titulo,
					icon: icone,
					url: '/seguranca/' + url
				},
				{
					title: 'Removido',
					active: true
				},
			],
			pageTitle: titulo + ' - Removido',
			rule: 'historicoAlteracao',
			parent: 'seguranca-' + url
		}
	}, {
		path: '/seguranca/' + url + '/removido/historico/:id',
		name: 'seguranca-' + url + '-removido-historico',
		component: () => import('@/views/apps/seguranca/Perfil/HistoricoRemovido/Index.vue'),
		props: true,
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Segurança'
				},
				{
					title: titulo,
					icon: icone,
					url: '/seguranca/' + url
				},
				{
					title: 'Removido',
					url: '/seguranca/' + url + '/removido'
				},
				{
					title: 'Histórico',
					active: true
				},
			],
			pageTitle: titulo + ' - Removido - Histórico',
			rule: 'historicoAlteracao',
			parent: 'seguranca-' + url
		}
	}, {
		path: '/seguranca/' + url + '/removido/historico/:id/:idHistorico',
		name: 'seguranca-' + url + '-removido-historico-visualizar',
		component: () => import('@/views/apps/seguranca/Perfil/HistoricoRemovido/VisualizarRegistro.vue'),
		props: true,
		meta: {
			breadcrumb: [
				{
					title: 'Início',
					url: '/'
				},
				{
					title: 'Segurança'
				},
				{
					title: titulo,
					icon: icone,
					url: '/seguranca/' + url
				},
				{
					title: 'Removido',
					url: '/seguranca/' + url + '/removido'
				},
				{
					title: 'Histórico',
					url: '/seguranca/' + url + '/removido/historico/:id'
				},
				{
					title: 'Visualizar',
					active: true
				},
			],
			pageTitle: titulo + ' - Removido - Histórico - Visualizar',
			rule: 'historicoAlteracao',
			parent: 'seguranca-' + url
		}
	}
];
