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
					let message = '';
					for (let prop in errors) {
						message += `<li>${errors[prop][0]}</li>`;
					}
					this.$toast.html(`<div class="snotifyToast__title"><b>${this.$translations.pleaseCorrect}</b></div><div class="snotifyToast__body"><ul>${message}</ul></div>`, {
						type: 'error'
					});
				}
			},
		}
	}
</script>
