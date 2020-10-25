import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);

var urlBasePath = '/';

const router = new Router({
	mode: 'history',
	base: urlBasePath,
	scrollBehavior () {
		return { x: 0, y: 0 };
	},
	routes: [
		{
			path: '',
			component: () => import('@/layouts/full-page/FullPage.vue'),
			children: [
				{
					path: '/callback',
					name: 'auth-callback',
					component: () => import('@/views/Callback.vue'),
					meta: {
						rule: 'editor'
					}
				},
				{
					path: '/login',
					name: 'page-login',
					component: () => import('@/views/pages/login/Login.vue'),
					meta: {
						rule: 'editor'
					}
				},
			]
		},
		{
			path: '*',
			redirect: '/pages/error-404'
		}
	],
});

router.afterEach((to, from) => {
	const appLoading = document.getElementById('loading-bg');
	if (appLoading) {
		appLoading.style.display = "none";
	}
});

export default router;
