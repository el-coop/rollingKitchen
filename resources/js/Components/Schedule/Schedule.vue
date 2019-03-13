<template>
	<ajax-form :action="action" @submitting="submitting = true" @errors="handleErrors" @submitted="handleSubmitted">
		<div class="box">
			<p v-text="`${$translations.budget}: ${budget}`"></p>
			<p v-text="`${$translations.budgetUsed}: ${budgetUsed}`"></p>
			<p v-if="budget < budgetUsed" class="help is-danger" v-text="$translations.budgetOverflow"></p>
		</div>
		<slot :submitting="submitting" :updateBudget="updateBudget"></slot>
	</ajax-form>
</template>

<script>
	export default {
		name: "Schedule",
		props: {
			action: {
				type: String,
				default: window.location.href
			},
			budget: {
				type: Number,
				required: true
			},
			initBudget: {
				type: Number,
				required: true
			}
		},
		data() {
			return {
				submitting: false,
				budgetUsed: this.initBudget
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
			updateBudget(value) {
				this.budgetUsed += value;
			}
		}
	}
</script>
