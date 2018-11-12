<script>
	export default {
		methods: {
			numToBoolTag(value) {
				if (value > 0) {
					return `<span class="tag is-success">${this.translations.yes}</span>`;
				}
				return `<span class="tag is-danger">${this.translations.no}</span>`;
			},

			translate(value) {
				return this.translations[value];
			}
		}
	}
</script>
