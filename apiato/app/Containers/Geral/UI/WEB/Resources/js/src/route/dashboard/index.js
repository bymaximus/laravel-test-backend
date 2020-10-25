var routes = [];

routes.push({
		path: '/',
		redirect: '/dashboard'
	}, {
		path: '/dashboard',
		name: 'dashboard-analytics',
		component: () => import('@/views/DashboardAnalytics.vue'),
		meta: {
			rule: 'admin',
		}
	}
);

Array.prototype.push.apply(routes, require('./seguranca/perfil.js'));
Array.prototype.push.apply(routes, require('./seguranca/usuario.js'));
Array.prototype.push.apply(routes, require('./seguranca/funcionalidade.js'));

Array.prototype.push.apply(routes, require('./cadastro/estado.js'));
Array.prototype.push.apply(routes, require('./cadastro/cidade.js'));
Array.prototype.push.apply(routes, require('./cadastro/imovel.js'));
Array.prototype.push.apply(routes, require('./cadastro/contrato.js'));

module.exports = {
	path: '',
	component: () => import('@/layouts/main/Main.vue'),
	children: routes
};
