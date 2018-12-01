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
				return this.error[0].replace(this.field.name.replace('[', '.').replace(']', ''), this.field.label);
			}
		}
	}
</script>

