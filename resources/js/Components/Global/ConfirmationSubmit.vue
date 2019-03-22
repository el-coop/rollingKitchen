<template>
	<button class="button" :class="buttonClass" @click="handleClick" v-text="label" :name="name"
			:value="value" :disabled="disabled"></button>
</template>
<script>
	export default {
		name: "ConfirmationSubmit",

		props: {
			buttonClass: {
				type: String,
				default: 'is-success'
			},

			label: {
				type: String,
				required: true
			},

			title: {
				type: String,
				required: true
			},

			subtitle: {
				type: String,
				required: true
			},

			yesText: {
				type: String,
				required: true
			},

			noText: {
				type: String,
				required: true
			},
			name: {
				type: String,
				default: ''
			},
			value: {
				type: String,
				default: ''
			}

		},

		data() {
			return {
				confirmed: false,
				disabled: false,
			}
		},
		methods: {
			handleClick(event) {
				if (!this.confirmed) {
					this.disabled = true;
					event.preventDefault();
					console.log(this.subtitle);
					this.$toast.question(this.subtitle, this.title, {
						close: false,
						timeout: false, position: 'center', buttons: [
							[`<button>${this.yesText}</button>`, this.handleConfirm, true],
							[`<button>${this.noText}</button>`, this.handleReject, false]
						]
					});
				}
			},
			handleReject(instance, toast) {
				this.disabled = false;
				instance.hide({transitionOut: 'fadeOut'}, toast, 'button');
			},
			async handleConfirm(instance, toast) {
				this.disabled = false;
				await this.$nextTick();
				this.confirmed = true;
				this.$el.click();
				instance.hide({transitionOut: 'fadeOut'}, toast, 'button');
				this.confirmed = false;
			}
		},
	}
</script>
