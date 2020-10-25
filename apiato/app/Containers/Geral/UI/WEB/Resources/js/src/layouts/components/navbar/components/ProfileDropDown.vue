<template>
	<div
		v-show="activeUserInfo && activeUserInfo.displayName"
		class="the-navbar__user-meta items-center"
		:class="[
            {'flex'            : activeUserInfo && activeUserInfo.displayName}
        ]"
	>

		<div
			v-show="activeUserInfo && activeUserInfo.displayName"
			class="text-right leading-tight hidden"
			:class="[
                {'sm:block'            : activeUserInfo && activeUserInfo.displayName}
            ]"
		>
			<p class="font-semibold">{{ activeUserInfo && activeUserInfo.displayName ? activeUserInfo.displayName : '' }}</p>
			<!--<small>Available</small>-->
		</div>

		<vs-dropdown
			vs-custom-content
			vs-trigger-click
			class="cursor-pointer"
		>

			<div class="con-img ml-3">
				<!--<img
					v-show="activeUserInfo && activeUserInfo.photoURL"
					key="onlineImg"
					:src="activeUserInfo && activeUserInfo.photoURL ? activeUserInfo.photoURL : null"
					:alt="activeUserInfo && activeUserInfo.photoURL ? 'user-img' : null"
					width="40"
					height="40"
					class="rounded-full shadow-md cursor-pointer"
					:class="[
                        {'block'            : activeUserInfo && activeUserInfo.photoURL}
                    ]"
				/>-->
				<vs-avatar
					color="primary"
					size="40px"
					icon-pack="far"
					icon="far fa-user"
				/>
			</div>

			<vs-dropdown-menu class="vx-navbar-dropdown">
				<ul style="min-width: 9rem">
					<!--<li
						class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
						@click="$router.push('/pages/profile').catch(() => {})"
					>
						<feather-icon
							icon="UserIcon"
							svgClasses="w-4 h-4"
						/>
						<span class="ml-2">Profile</span>
					</li>
					<li
						class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
						@click="$router.push('/apps/email').catch(() => {})"
					>
						<feather-icon
							icon="MailIcon"
							svgClasses="w-4 h-4"
						/>
						<span class="ml-2">Inbox</span>
					</li>
					<li
						class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
						@click="$router.push('/apps/todo').catch(() => {})"
					>
						<feather-icon
							icon="CheckSquareIcon"
							svgClasses="w-4 h-4"
						/>
						<span class="ml-2">Tasks</span>
					</li>
					<li
						class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
						@click="$router.push('/apps/chat').catch(() => {})"
					>
						<feather-icon
							icon="MessageSquareIcon"
							svgClasses="w-4 h-4"
						/>
						<span class="ml-2">Chat</span>
					</li>
					<li
						class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
						@click="$router.push('/apps/eCommerce/wish-list').catch(() => {})"
					>
						<feather-icon
							icon="HeartIcon"
							svgClasses="w-4 h-4"
						/>
						<span class="ml-2">Wish List</span>
					</li>
					<vs-divider class="m-1" />-->
					<li
						class="flex py-2 px-4  hover:bg-danger hover:text-white"
						:class="[
              {'cursor-pointer'            : !busy        },
              {'disabled-item pointer-events-none': busy }
            ]"
						@click="logout"
					>
						<feather-icon
							icon="LogOutIcon"
							svgClasses="w-4 h-4"
						/>
						<span class="ml-2">Sair</span>
					</li>
				</ul>
			</vs-dropdown-menu>
		</vs-dropdown>
	</div>
</template>

<script>
export default {
	data() {
		return {
			busy: false,
		};
	},
	computed: {
		activeUserInfo() {
			return this.$store.getters.isUserLoggedIn &&
				this.$store.state.AppActiveUser
				? this.$store.state.AppActiveUser
				: null;
		},
	},
	methods: {
		logout() {
			if (!this.$store.getters.isUserLoggedIn) {
				return;
			}
			if (this.busy) {
				this.$vs.notify({
					title: "Error",
					text: "Already logging out...",
					iconPack: "feather",
					icon: "icon-alert-circle",
					color: "danger",
				});
				return;
			}
			this.$vs.loading();
			this.busy = true;
			this.$store
				.dispatch("auth/logoutJWT")
				.then((response) => {
					this.$vs.loading.close();
					this.$vs.notify({
						title: "Saindo",
						text: "Redirecionando...",
						iconPack: "feather",
						icon: "icon-user",
						color: "success",
					});
					setTimeout(() => {
						document.location.href = this.$urlBasePath;
					}, 1000);
				})
				.catch((error) => {
					this.$vs.loading.close();
					var errMsg = "Request error.";
					if (
						error &&
						error.response &&
						error.response.data &&
						error.response.data.error
					) {
						errMsg = error.response.data.error;
					} else if (
						error &&
						error.response &&
						error.response.data &&
						error.response.data.message
					) {
						errMsg = error.response.data.message;
					} else if (error && error.message) {
						errMsg = error.message;
					}
					this.$vs.notify({
						title: "Error",
						text: error.message,
						iconPack: "feather",
						icon: "icon-alert-circle",
						color: "danger",
					});
					if (
						error &&
						error.response &&
						(error.response.status == 428 ||
							error.response.status == 401)
					) {
						setTimeout(() => {
							document.location.reload();
						}, 1000);
					} else {
						this.busy = false;
					}
				});
		},
	},
};
</script>
<style lang="scss">
div.vx-navbar-dropdown div.vs-dropdown--custom {
	padding: 0px !important;
	border: 0px !important;
}
div.vx-navbar-dropdown div.vs-dropdown--custom ul li:first-child:hover {
	border-top-left-radius: 5px !important;
	border-top-right-radius: 5px !important;
}
div.vx-navbar-dropdown div.vs-dropdown--custom ul li:last-child:hover {
	border-bottom-left-radius: 5px !important;
	border-bottom-right-radius: 5px !important;
}
div.vx-navbar-dropdown div.vs-dropdown--custom ul div.vs-divider {
	margin: 0px !important;
}
</style>
