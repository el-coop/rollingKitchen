<template>
	<modal :name="name" transition="slide-up-opacity" :height="height" :adaptive="true"
		   :width="width"
		   :pivotY="pivotY"
		   :pivotX="pivotX"
		   ref="modal">
		<div class="box h-100 modal-body">
			<button class="modal-close is-large" @click="$modal.hide(name)"></button>
			<slot/>
		</div>
	</modal>
</template>

<script>
	export default {
		name: "modal-component",
		props: {
			name: {
				type: String,
				required: true
			},
			width: {
				default: 600
			},
			height: {
				default: 'auto'
			},
			pivotY: {
				type: Number,
				default: 0.5
			},
			pivotX: {
				type: Number,
				default: 0.5
			}
		},
		data() {
			return {
				isOpen: false
			};
		}
	}
</script>

<style lang="scss">
	@import "../../../sass/variables";

	.modal-body {
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
