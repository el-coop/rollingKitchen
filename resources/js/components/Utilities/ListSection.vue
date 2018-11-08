<template>
	<div :class="{'menu-section-wrapper--open' : open}">
		<p class="menu-label" v-text="label" @click="open = !open">

		</p>
		<ul class="menu-list" :style="style" ref="list">
			<slot></slot>
		</ul>
	</div>
</template>

<script>
	export default {
		name: "ListSection",

		props: {
			label: {
				required: true,
				Type: String
			},
			startOpen: {
				default() {
					return false;
				},
				type: Boolean
			}
		},

		data() {
			return {
				open: true,
				height: null
			}
		},

		mounted() {
			this.height = this.$refs.list.scrollHeight;
			if (!this.startOpen) {
				this.open = false;
			}
		},

		computed: {
			style() {
				if (this.open) {
					return {height: `${this.height}px`};
				}

				return {height: 0};
			}
		}
	}
</script>

<style scoped lang="scss">
	@import "../../../sass/variables";

	.menu-section-wrapper--open {
		margin-bottom: 0.5em;
	}

	.menu-label {
		padding: 0.5em 0.75em;
		cursor: pointer;

		&:hover {
			background-color: $menu-item-hover-background-color;
			color: $menu-item-hover-color;
		}
	}

	.menu-list {
		transition: height 0.5s;

		overflow: hidden;

		& > li > a {
			padding-left: 1em;
		}
	}

</style>