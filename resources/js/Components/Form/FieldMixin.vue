<script>
	export default {
		props: {
			field: {
				required: true,
				type: Object
			},
			error: {
				default: null,
				type: Array
			}
		},
		data() {
			return {
				value: this.field.value
			}
		},

		computed: {
			errorText() {
				if (!this.error) {
					return '';
				}
				return this.error[0].replace(this.field.name.replace('[', '.').replace(']', '').replace('_', ' '), this.field.label);
			}
		},

		watch: {
			value(value) {
				this.$emit('input', value);
			}
		}
	}
</script>

