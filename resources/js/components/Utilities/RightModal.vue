<template>
	<modal :name="name" :adaptive="true" height="auto" :pivotY="0"
		   :pivotX="1">
		<div class="modal-body">
			<slot></slot>
		</div>
	</modal>
</template>

<script>
	export default {
		name: "RightModal",
		props: {
			name: {
				type: String,
				default() {
					return 'right-modal';
				}
			}
		}
	}
</script>

<style scoped lang="scss">
	@import "../../../sass/variables";

	.modal-body {
		height: 100vh;
		overflow-x: hidden;
		overflow-y: auto;
	}
</style>