<template>
	<div v-if="active">
		<slot></slot>
	</div>
</template>

<script>
	export default {
		name: "SelectView",
		props: {
			label: {
				type: String,
				required: true
			},
		},

		created() {
			this.$parent.views.push(this);
		},

		data() {
			return {
				active: false
			}
		},

		mounted() {
			this.active = this.$parent.selected == this.index;
		},

		computed: {
			index() {
				return this.$parent.views.indexOf(this)
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