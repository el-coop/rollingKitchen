<template>
	<button class="button" :class="buttonClass" @click="handleClick" v-text="label" :name="name"
			:value="value"></button>
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
			},

		},

		data() {
			return {
				confirmed: false
			}
		},

		methods: {
			handleClick(event) {
				if (!this.confirmed) {
					event.preventDefault();

					this.$toast.question(this.subtitle, this.title, {
						timeout: false, position: 'center', buttons: [
							[`<button>${this.yesText}</button>`, this.handleConfirm, true],
							[`<button>${this.noText}</button>`, this.handleReject, false]
						],
					});
				}
			},
			handleReject(instance, toast) {
				instance.hide({transitionOut: 'fadeOut'}, toast, 'button');
			},
			handleConfirm(instance, toast) {
				this.confirmed = true;
				this.$el.click();
				instance.hide({transitionOut: 'fadeOut'}, toast, 'button');
			}
		}
	}
</script>