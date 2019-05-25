<template>
	<ajax-form method="patch" @errors="handleErrors" @submitting="submitting = true" :action="url"
			   @submitted="submitted">
		<slot></slot>
		<div class="buttons">
			<button class="button is-fullwidth is-success" :class="{'is-loading' : submitting}"
					type="submit" v-text="$translations.save">
			</button>
		</div>
	</ajax-form>
</template>

<script>
	export default {
		name: "ServicesForm",

		props: {
			url: {
				type: String,
				required: true
			}
		},

		data() {
			return {
				submitting: false,
			}
		},

		methods: {

			handleErrors(errors) {
				this.errors = errors;
				if (Object.keys(this.errors).length) {
					this.$toast.error(this.$translations.pleaseCorrect, this.$translations.formErrors);
				}
			},

			submitted(response) {
				this.submitting = false;
				this.alternativeSubmitting = false;
				if (response.status === 200 || response.status === 201) {
					console.log('here');
					this.$toast.success(this.$translations.updateSuccess);
				}
			},
		}
	}
</script>
