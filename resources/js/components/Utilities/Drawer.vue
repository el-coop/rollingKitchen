<template>
	<div class="drawer" :class="{'drawer--open': open}">
		<slot></slot>
	</div>
</template>

<script>
	export default {
		name: "Drawer",

		data() {
			return {
				open: false
			}
		}
	}
</script>

<style scoped lang="scss">
	@import "../../../sass/variables";

	.drawer {
		z-index: 100;
		position: absolute;
		top: 0;
		left: 0;
		padding-top: 1em;
		background-color: $contrast-bg;
		width: 0;
		height: 100%;
		overflow: hidden;
		transition: width 0.3s;
		@media #{$above-tablet}{
			position: static;
			width: 200px;
		}

		&.drawer--open {
			width: 100%;

			@media #{$above-tablet}{
				width: 200px;
			}

		}
	}
</style>