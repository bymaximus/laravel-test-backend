import navbarSearchAndPinList from "@/layouts/components/navbar/navbarSearchAndPinList";
import themeConfig from "@/../themeConfig.js";
import colors from "@/../themeConfig.js";

// /////////////////////////////////////////////
// Helper
// /////////////////////////////////////////////

// *From Auth - Data will be received from auth provider
const userDefaults = {
	uid: null,
	idPerfil: null,
	idUsuario: null,
	displayName: null,
	about: null,
	photoURL: null,
	status: null,
	userRole: null,
	token: null,
	menu: []
};

const userInfoLocalStorage = JSON.parse(localStorage.getItem(themeConfig.userInfoLocalStorageKey)) || {};

// Set default values for active-user
// More data can be added by auth provider or other plugins/packages
const getUserInfo = () => {
	let userInfo = {};

	// Update property in user
	Object.keys(userDefaults).forEach((key) => {
		// If property is defined in localStorage => Use that
		userInfo[key] = userInfoLocalStorage[key] ? userInfoLocalStorage[key] : userDefaults[key];
	});

	// Include properties from localStorage
	Object.keys(userInfoLocalStorage).forEach((key) => {
		if (userInfo[key] == undefined && userInfoLocalStorage[key] != null) userInfo[key] = userInfoLocalStorage[key];
	});

	return userInfo;
};

// /////////////////////////////////////////////
// State
// /////////////////////////////////////////////

const state = {
	AppActiveUser: getUserInfo(),
	logoutAsked: false,
	bodyOverlay: false,
	isVerticalNavMenuActive: true,
	mainLayoutType: themeConfig.mainLayoutType || "vertical",
	navbarSearchAndPinList: navbarSearchAndPinList,
	reduceButton: themeConfig.sidebarCollapsed,
	verticalNavMenuWidth: "default",
	verticalNavMenuItemsMin: false,
	scrollY: 0,
	starredPages: navbarSearchAndPinList["pages"].data.filter((page) => page.is_bookmarked),
	theme: themeConfig.theme || "light",
	themePrimaryColor: colors.primary,

	// Can be used to get current window with
	// Note: Above breakpoint state is for internal use of sidebar & navbar component
	windowWidth: null,
	routerIsBusy: false,
};

export default state;
