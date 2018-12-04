<template>
	<modal :name="name" transition="slide-up-opacity" :height="height" :adaptive="true"
		   :width="width"
		   :pivotY="pivotY"
		   :pivotX="pivotX"
		   ref="modal">
		<div class="box h-100 modal-body">
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

<style scoped>
	.modal-body{
		overflow-y: auto;
	}
</style>

