<template>
	<modal name="datatable-row" :adaptive="true" height="100%" :pivotY="0"
		   :pivotX="1" :width="width">
		<div class="modal-body box">
			<button class="modal-close is-large" @click="$modal.hide('datatable-row')"></button>
			<slot></slot>
		</div>
	</modal>
</template>

<script>
	export default {
		name: "DatatableRowDisplay",
		props: {
			width: {
				default: 600
			}
		}
	}
</script>

<style lang="scss">
	@import "../../../../sass/variables";

	.modal-body {
		height: 100vh;
		overflow-x: hidden;
		overflow-y: auto;

		> *:last-child {
			margin-bottom: 1rem;
		}

		.modal-close {
			position: relative;
			float: right;
			top: 0;
			right: 0;

			&:after, &:before {
				background: $contrast-bg;
			}
		}
	}
</style>