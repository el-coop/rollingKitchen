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
		}
	}
</script>

