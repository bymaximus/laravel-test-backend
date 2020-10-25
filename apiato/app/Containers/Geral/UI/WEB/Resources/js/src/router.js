
import Vue from 'vue';
import Router from 'vue-router';
import themeConfig from "@/../themeConfig.js";


var urlBasePath = '/';
var routes = [];

routes.push(
	require('@/route/dashboard/index.js'), {
		path: '',
		component: () => import('@/layouts/full-page/FullPage.vue'),
		children: [
			{
				path: '/unauthorized',
				name: 'page-unauthorized',
				component: () => import('@/views/pages/NotAuthorized.vue'),
				meta: {
					rule: themeConfig.userDefaultRole
				}
			}
		]
	}
);

Vue.use(Router);

const router = new Router({
	mode: 'history',
	base: urlBasePath,
	scrollBehavior() {
		return {
			x: 0,
			y: 0
		};
	},
	routes: routes
});

let startRouter = new Promise(resolve => {
	router.start = resolve;
});
startRouter.pending = true;

router.isBusy = false;

router.afterEach((to, from) => {
	let appLoading = document.getElementById('loading-bg');
	if (appLoading) {
		appLoading.style.display = "none";
		router.isBusy = false;
	}
});

router.beforeEach(async (to, from, next) => {
	router.isBusy = true;
	if (startRouter.pending) {
		await startRouter;
		startRouter.pending = false;
	}
	if (to.meta.authRequired) {
		/*if (!auth.isAuthenticated()) {
		    //router.push({ path: '/pages/login', query: { to: to.path } })
		    document.location.href = '/';
		    return;
		}*/
	}
	return next();
});
export default router;
