<template>
	<modal-component name="fireworks">
		<div class="has-text-centered is-size-4" v-html="text">
		</div>
		<figure class="image is-4by3">
			<img src="/images/proost.gif">
		</figure>
	</modal-component>
</template>

<script>
	export default {
		name: "fireworks-modal",

		props: {
			text: {
				type: String,
				default: ''
			}
		},

		mounted() {
			this.$modal.show('fireworks');
		}
	}
</script>
