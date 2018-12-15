<template>
	<div v-show="active">
		<slot></slot>
	</div>
</template>

<script>
	import PaginationPartialMixin from '../PaginationPartialMixin';

	export default {
		name: "Tab",
		mixins: [PaginationPartialMixin],

		props: {
			icon: {
				type: String,
				default: ''
			},
			startOpen: {
				type: Boolean,
				default: false
			}
		},

		mounted() {
			if (this.startOpen) {
				this.$parent.selected = this.index;
			}
		},
	}
</script>
