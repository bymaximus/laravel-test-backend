let titulo = 'Funcionalidade';
let view = 'Funcionalidade';
let url = 'funcionalidade';
let icone = 'fa fa-tv';


module.exports = [
	{
		path: '/seguranca/' + url,
		name: 'seguranca-' + url,
		component: () => import('@/views/apps/seguranca/Funcionalidade.vue'),
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
	}
];
