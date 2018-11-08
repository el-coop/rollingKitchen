<template>
	<div class="drawer" :class="{'drawer--open': open}">
		<div class="is-hidden-tablet is-pulled-right">
			<button class="delete is-medium" @click="open=false"></button>
		</div>
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
		},

		mounted() {
			this.$bus.$on('open-drawer', () => {
				this.open = true
			});
		},

		beforeDestroy() {
			this.$bus.$off('open-drawer');
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

		.delete {
			margin-right: 10px;
			border: solid 1px $background;
			border-radius: 100%;
		}

		&.drawer--open {
			width: 100%;

			@media #{$above-tablet}{
				width: 200px;
			}

		}
	}
</style>