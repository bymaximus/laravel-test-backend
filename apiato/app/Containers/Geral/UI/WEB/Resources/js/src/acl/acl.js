import Vue from "vue";
import {
	AclInstaller,
	AclCreate,
	AclRule
} from "vue-acl";
import router from "@/router";
import themeConfig from "@/../themeConfig.js";

Vue.use(AclInstaller);

let rules = {
	admin: new AclRule("admin").generate(),
	historicoAlteracao: new AclRule("historicoAlteracao").generate(),
	modificar: new AclRule("modificar").generate(),
	editor: new AclRule("editor").or("admin").generate(),
};
rules[themeConfig.userDefaultRole] = new AclRule(themeConfig.userDefaultRole).or("admin").or("editor").generate();

export default new AclCreate({
	initial: themeConfig.userDefaultRole,
	notfound: "/unauthorized",
	router,
	acceptLocalRules: true,
	globalRules: rules
});
