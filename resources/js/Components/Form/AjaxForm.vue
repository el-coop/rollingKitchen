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
				const data = {};
				formData.forEach((value, key) => {
					// Check if property already exist
					if (Object.prototype.hasOwnProperty.call(data, key)) {
						let current = data[key];
						if (!Array.isArray(current)) {
							// If it's not an array, convert it to an array.
							current = data[key] = [current];
						}
						current.push(value); // Add the new value to the array.
					} else {
						data[key] = value;
					}
				});
				return data;
				console.log(data);

				formData.forEach((value, key) => {
					console.log(key, formData.get(key), formData.getAll(key), value);
					return;
					if (key.indexOf('[') > -1 && key.indexOf(']') > key.indexOf('[')) {
						const keyStart = key.indexOf('[');
						const keyArrayName = key.substr(0, keyStart);
						const keyName = key.substr(keyStart + 1, key.indexOf(']') - keyStart - 1);
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

			async submit(event) {
				this.clearErrors();
				let response;
				const data = this.getData();
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
					response = await axios[this.method](this.action, data, options);
				} catch (error) {
					response = error.response;
					if (response.data.errors) {
						this.formatErrors(response.data.errors);
					}
				}
				this.$emit('submitted', response);
			},

			formatErrors(errors) {
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
