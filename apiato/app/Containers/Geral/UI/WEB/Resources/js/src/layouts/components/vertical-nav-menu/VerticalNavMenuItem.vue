<template>
	<div
		v-show="canSee"
		class="vs-sidebar--item"
		:class="[
      {'vs-sidebar-item-active'            : activeLink},
      {'disabled-item pointer-events-none' : isDisabledFromRule}
    ]"
	>
		<router-link
			tabindex="-1"
			v-if="to"
			exact
			:class="[{'router-link-active': activeLink}]"
			:to="to"
			:target="target"
		>
			<vs-icon
				v-if="!featherIcon"
				:icon-pack="iconPack"
				:icon="icon"
			/>
			<feather-icon
				v-else
				:class="{'w-3 h-3': iconSmall}"
				:icon="icon"
			/>
			<slot />
		</router-link>

		<a
			v-else
			:target="target"
			:href="href"
			tabindex="-1"
		>
			<vs-icon
				v-if="!featherIcon"
				:icon-pack="iconPack"
				:icon="icon"
			/>
			<feather-icon
				v-else
				:class="{'w-3 h-3': iconSmall}"
				:icon="icon"
			/>
			<slot />
		</a>
	</div>
</template>

<script>
export default {
	name: "v-nav-menu-item",
	props: {
		icon: { type: String, default: "" },
		iconSmall: { type: Boolean, default: false },
		iconPack: { type: String, default: "material-icons" },
		href: { type: [String, null], default: "#" },
		to: { type: [String, Object, null], default: null },
		slug: { type: String, default: null },
		index: { type: [String, Number], default: null },
		featherIcon: { type: Boolean, default: false },
		target: { type: String, default: "_self" },
		isDisabled: { type: Boolean, default: false },
	},
	data: () => ({
		isDisabledRouter: false,
	}),
	watch: {
		"$store.state.routerIsBusy"(val) {
			this.isDisabledRouter = val;
		},
	},
	computed: {
		isDisabledFromRule() {
			return (
				this.isDisabled ||
				this.isDisabledRouter ||
				(this.$store &&
					this.$store.state &&
					this.$store.state.routerIsBusy) ||
				(this.to &&
					(!this.$store ||
						!this.$store.getters.isUserLoggedIn ||
						!this.$store.state.AppActiveUser ||
						!this.$store.state.AppActiveUser.userRole ||
						!this.$acl ||
						!this.$acl.get ||
						!this.$router ||
						!this.$router.match(this.to).meta.rule ||
						!this.$acl.check(
							this.$router.match(this.to).meta.rule
						)))
			);
		},
		canSee() {
			return true;
			return this.to
				? this.$acl.check(this.$router.match(this.to).meta.rule)
				: true;
		},
		activeLink() {
			return this.to == this.$route.path ||
				(this.$route.meta.parent == this.slug && this.to)
				? true
				: false;
		},
	}
};
</script>

