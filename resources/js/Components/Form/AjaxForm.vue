<template>
	<form @submit.prevent="submit">
		<slot name="errors" v-if="errors" :error="errors"></slot>
		<slot></slot>
	</form>
</template>

<script>
	export default {
		name: "ajax-form",

		props: {
			method: {
				default: 'post',
				type: String
			},
			headers: {
				default() {
					return {
						'Content-type': 'application/json'
					};
				},
				type: Object
			},
			extraData: {
				default() {
					return {};
				},
				type: Object
			},
			action: {
				type: String,
				default: window.location
			},
		},

		data() {
			return {
				errors: {}
			}
		},
		methods: {
			getData() {
				let data = new FormData(this.$el);
				for (let prop in this.extraData) {
					if (this.extraData.hasOwnProperty(prop)) {
						data.append(prop, this.extraData[prop])
					}
				}
				if (this.headers['Content-type'] === 'application/json') {
					return this.jsonify(data);
				}
				return data;
			},

			jsonify(formData) {
				let data = {};
				formData.forEach((value, key) => {
					if (key.indexOf('[') > -1 && key.indexOf(']') > key.indexOf('[')) {
						const keyStart = key.indexOf('[');
						const keyArrayName = key.substr(0, keyStart);
						const keyName = key.substr(keyStart + 1, key.indexOf(']') - keyStart-1);
						if (!data[keyArrayName]) {
							data[keyArrayName] = {};
						}
						data[keyArrayName][keyName] = value;
					} else {
						data[key] = value;
					}
				});
				return data;
			},

			async submit() {
				this.clearErrors();
				this.$emit('submitting');
				let response;
				try {
					response = await axios[this.method](this.action, this.getData(), this.headers);
				} catch (error) {
					response = error.response;
					if (response.data.errors) {
						this.formatErrors(response.data.errors);
					}
				}
				this.$emit('submitted', response);
			},

			formatErrors(errors) {
				console.log(errors);
				this.errors = errors;
				this.$emit('errors', this.errors);
			},


			clearErrors(errors) {
				this.errors = {};
				this.$emit('errors', this.errors);
			}
		},
	}
</script>

<style scoped lang="scss">
	form.is-fullwidth {
		width: 100%;
	}
</style>