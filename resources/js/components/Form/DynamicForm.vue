<template>
	<form>
		<div v-if="loading" class="has-text-centered">
			<a class="button is-loading"></a>
		</div>
		<component v-for="field in fields" :is="`${field.type}-field`" :field="field">

		</component>
	</form>
</template>

<script>
	import TextField from './TextField';
	import TextareaField from './TextareatField';

	export default {
		name: "DynamicForm",
		components: {
			TextField,
			TextareaField
		},

		props: {
			url: {
				type: String,
				default: ''
			},
			initFields: {
				type: Object,
				default() {
					return null;
				}
			}
		},

		data() {
			return {
				fields: [],
				loading: false
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
		}
	}
</script>
