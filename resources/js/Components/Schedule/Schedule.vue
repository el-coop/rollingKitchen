<template>
	<ajax-form :action="action" @submitting="submitting = true" @errors="handleErrors" @submitted="handleSubmitted">
		<slot :submitting="submitting"></slot>
	</ajax-form>
</template>

<script>
	export default {
		name: "Schedule",
		props: {
			action: {
				type: String,
				default: window.location.href
			}
		},
		data() {
			return {
				submitting: false
			}
		},

		methods: {
			handleSubmitted(response) {
				this.submitting = false;
				if (response.status > 199 && response.status < 300) {
					this.$toast.success(this.$translations.updateSuccess);
				}
			},

			handleErrors(errors) {
				if (Object.keys(errors).length) {
					this.$toast.error(errors[Object.keys(errors)[0]][0], this.$translations.formErrors, {
						type: 'error'
					});
				}
			},
		}
	}
</script>
