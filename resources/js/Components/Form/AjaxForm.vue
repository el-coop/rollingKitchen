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
				default: window.location.href
			},
		},

		data() {
			return {
				errors: {},
				realMethod: this.method
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
				} else if (this.method !== 'post' && this.method !== 'get') {
					data.append('_method', this.method);
					this.realMethod = "post";
				}
				return data;
			},

			jsonify(formData) {
				const data = {};

				formData.forEach((value, key) => {
					const keyVals = key.replace(/\]/g, '').split('[');
					let lastUpdated = data;
					let lastKey;
					keyVals.forEach((keyName, index) => {
						if (!lastUpdated[keyName]) {
							lastUpdated[keyName] = {};
						}
						if (index < keyVals.length - 1) {
							lastUpdated = lastUpdated[keyName];
						}
						lastKey = keyName;
					});
					lastUpdated[lastKey] = value;
				});
				return data;
			},

			async submit() {
				const data = this.getData();
				let response;

				const options = {
					headers: this.headers,
				};

				if (data.file_download) {
					options.responseType = 'blob';
					this.$emit('alternative-submitting');
				} else {
					this.$emit('submitting');
				}
				try {
					response = await axios[this.realMethod](this.action, data, options);
				} catch (error) {
					if (options.responseType === 'blob') {
						response = await this.handleBlobError(error.response)
					} else {
						response = error.response;
					}
					if (response.data.errors) {
						this.formatErrors(response.data.errors);
					} else {
						if (response.status === 419) {
							this.$toast.error(this.$translations.sessionExpired);
						} else {
							this.$toast.error(this.$translations.generalError);
						}
					}
				}
				this.realMethod = this.method;
				this.$emit('submitted', response);
			},

			formatErrors(errors) {
				this.errors = errors;
				this.$emit('errors', this.errors);
			},


			clearErrors(errors) {
				this.errors = {};
				this.$emit('errors', this.errors);
			},

			async handleBlobError(response) {
				try {
					const parsedData = await new Promise((resolve, reject) => {
						const reader = new FileReader();
						reader.addEventListener('abort', (error) => {
							reject(error);
						});
						reader.addEventListener('error', (error) => {
							reject(error);
						});
						reader.addEventListener('loadend', (event) => {
							resolve(reader.result);
						});
						reader.readAsText(response.data)
					});
					response.data = JSON.parse(parsedData);
					return response;
				} catch (error) {
					return {
						data: {},
						status: 500
					};
				}
			}
		},

		watch: {
			method(value) {
				this.realMethod = value;
			}
		}
	}
</script>

<style scoped lang="scss">
	form.is-fullwidth {
		width: 100%;
	}
</style>
