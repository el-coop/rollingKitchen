<template>
	<div v-show="active">
		<slot></slot>
	</div>
</template>

<script>
	export default {
		name: "Tab",

		props: {
			label: {
				type: String,
				required: true
			},
			icon: {
				type: String,
				default: ''
			},
			startOpen: {
				type: Boolean,
				default: false
			}
		},

		created() {
			this.$parent.tabs.push(this);
		},

		data() {
			return {
				active: false
			}
		},

		mounted() {
			if (this.startOpen) {
				this.$parent.selected = this.index();
			}
			this.active = this.$parent.selected == this.index;
		},

		computed: {
			index() {
				return this.$parent.tabs.indexOf(this)
			}
		},

		watch: {
			'$parent.selected'(index) {
				this.active = index == this.index;
			}
		}
	}
</script>

<style scoped>

</style>