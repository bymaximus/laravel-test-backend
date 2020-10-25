
<template functional>
	<footer
		class="the-footer flex-wrap justify-between"
		:class="classes"
	>
		<span></span>
		<span class="md:flex hidden items-center">
			<span></span>
		</span>
	</footer>
</template>

<script>
export default {
	name: "the-footer",
	props: {
		classes: {
			type: String
		}
	}
};
</script>
