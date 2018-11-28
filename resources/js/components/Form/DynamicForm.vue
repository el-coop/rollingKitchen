<template>
    <ajax-form :headers="headers" @errors="handleErrors" :method="method" :action="url" @submitting="submitting = true"
               @submitted="submitted" :extraData="extraData">
        <div v-if="loading" class="has-text-centered">
            <a class="button is-loading"></a>
        </div>
        <component v-if="hide.indexOf(field.name) === -1"
                   :error="errors[field.name.replace('[','.').replace(']','')] || null"
                   v-for="(field,key) in fields" :is="`${field.type}-field`"
                   :field="field" :key="key">
        </component>
        <button v-if="!loading" class="button is-fullwidth" :class="[submitting ? 'is-loading' : '', buttonClass]"
                type="submit">Save
        </button>
    </ajax-form>
</template>

<script>
	import TextField from './TextField';
	import SelectField from './SelectField';
	import TextareaField from './TextareatField';

	export default {
		name: "DynamicForm",
		components: {
			TextField,
			TextareaField,
			SelectField,
		},

		props: {
			url: {
				type: String,
				default: ''
			},
			initFields: {
				type: Array,
				default() {
					return null;
				}
			},
			method: {
				type: String,
				default: 'patch'
			},
			onDataUpdate: {
				type: Function,
				default(data) {
					this.$emit('object-update', data);
				}
			},
			hide: {
				type: Array,
				default() {
					return [];
				}
			},
			buttonClass: {
				type: String,
				default: 'is-success'
			},
			extraData: {
				type: Object,
				default() {
					return {};
				}
			},
			headers: {
				type: Object,
				default() {
					return {
						'Content-type': 'application/json'
					};
				}
			}
		},

		data() {
			return {
				fields: [],
				loading: false,
				submitting: false,
				errors: {}
			};
		},

		async created() {
			if (this.initFields) {
				return this.fields = this.initFields;
			}

			try {
				this.loading = true;
				const response = await axios.get(this.url);

				this.fields = response.data;
			} catch (error) {
				this.$toast.error('Please try again later', 'Operation failed');
			}
			this.loading = false;
		},

		methods: {
			handleErrors(errors) {
				this.errors = errors;
				if (Object.keys(this.errors).length) {
					this.$toast.error('Please correct them', "There are some errors in your form");
				}
			},

			submitted(response) {
				this.submitting = false;
				if (response.status === 200 || response.status === 201) {
					this.$toast.success('Update successful');
					this.onDataUpdate(response.data);
				}
			},

		},
	}
</script>
