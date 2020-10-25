let colors = {
	primary: '#2d2d2d',
	success: '#16cc65',
	danger: '#ef5160',
	warning: '#edcc3d',
	dark: '#1E1E1E',
};

const themeConfig = {
	disableCustomizer: false, // options[Boolean] : true, false(default)
	disableThemeTour: true, // options[Boolean] : true, false(default)
	footerType: "static", // options[String]  : static(default) / sticky / hidden
	hideScrollToTop: false, // options[Boolean] : true, false(default)
	mainLayoutType: "vertical", // options[String]  : vertical(default) / horizontal
	navbarColor: "#2d2d2d", // options[String]  : HEX color / rgb / rgba / Valid HTML Color name - (default: #fff)
	navbarType: "floating", // options[String]  : floating(default) / static / sticky / hidden
	routerTransition: "zoom-fade", // options[String]  : zoom-fade / slide-fade / fade-bottom / fade / zoom-out / none(default)
	rtl: false, // options[Boolean] : true, false(default)
	sidebarCollapsed: false, // options[Boolean] : true, false(default)
	theme: "semi-dark", // options[String]  : "light"(default), "dark", "semi-dark"
	// Not required yet - WIP
	userInfoLocalStorageKey: "userInfo",
	// NOTE: themeTour will be disabled in screens < 1200. Please refer docs for more info.
	checkLoggedUserInterval: (120 * 1000),
	userDefaultRole: 'public',
	menuTitle: 'Accordous'
};

import Vue from 'vue';
import Vuesax from 'vuesax';
Vue.use(Vuesax, {
	theme: {
		colors
	},
	rtl: themeConfig.rtl
});

export default themeConfig;
