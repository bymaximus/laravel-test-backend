<template functional>
	<div
		class="vx-breadcrumb"
		:class="data.staticClass"
	>
		<ul class="flex flex-wrap items-center">
			<li class="inline-flex items-end">
				<router-link to="/">
					<vs-icon
						v-if="props.route.meta.breadcrumb[0] && props.route.meta.breadcrumb[0].hasOwnProperty('url') && props.route.meta.breadcrumb[0].url == '/' && props.route.meta.breadcrumb[0].hasOwnProperty('icon') && props.route.meta.breadcrumb[0].icon"
						icon-pack="fa"
						:icon="props.route.meta.breadcrumb[0].icon"
					/>
					<feather-icon
						v-else
						icon="HomeIcon"
						svgClasses="h-5 w-5 mb-1 stroke-current text-primary"
						class="flex self-center"
					/>
				</router-link>
				<span class="breadcrumb-separator mx-2 flex self-center">
					<feather-icon
						:icon="props.isRTL ? 'ChevronsLeftIcon' : 'ChevronsRightIcon'"
						svgClasses="w-4 h-4"
					/></span>
			</li>
			<li
				v-for="(link, index) in props.route.meta.breadcrumb.slice(1,-1)"
				:key="index"
				class="inline-flex items-center"
			>
				<vs-icon
					v-if="link.hasOwnProperty('icon') && link.icon"
					icon-pack="fa"
					:icon="link.icon"
					class="mr-2 self-center"
				/>
				<router-link
					:to="props.processBreadCrumbUrl(link.url)"
					v-if="link.url"
				>{{ link.title }}</router-link>
				<span
					class="text-primary cursor-default"
					v-else
				>{{ link.title }}</span>
				<span class="breadcrumb-separator mx-2 flex items-start">
					<feather-icon
						:icon="props.isRTL ? 'ChevronsLeftIcon' : 'ChevronsRightIcon'"
						svgClasses="w-4 h-4"
					/></span>
			</li>
			<li class="inline-flex">
				<vs-icon
					v-if="props.route.meta.breadcrumb.slice(-1)[0].hasOwnProperty('icon') && props.route.meta.breadcrumb.slice(-1)[0].icon"
					icon-pack="fa"
					:icon="props.route.meta.breadcrumb.slice(-1)[0].icon"
					class="mr-2 self-center"
				/>
				<span
					v-if="props.route.meta.breadcrumb.slice(-1)[0].active"
					class="cursor-default"
				>{{ props.route.meta.breadcrumb.slice(-1)[0].title }}</span>
			</li>
		</ul>
	</div>
</template>

<script>
export default {
	name: "vx-breadcrumb",
};
</script>
