<script>
	export default {
		methods: {
			numToBoolTag(value) {
				if (value > 0) {
					return `<span class="tag is-success">${this.$translations.yes}</span>`;
				}
				return `<span class="tag is-danger">${this.$translations.no}</span>`;
			},

			translate(value) {
				return this.$translations[value];
			},

			localNumber(value) {
				console.log(value);
				return new Intl.NumberFormat(document.documentElement.lang, {
					minimumFractionDigits: 2
				}).format(value);
			},

			paidStatus(value) {
				if (value) {
					return `<span class="tag is-success">${this.$translations.paid}</span>`;
				}
				return `<span class="tag is-danger">${this.$translations.unpaid}</span>`;
			},

			prefix(value, {callbackOptions}) {
				return `${callbackOptions.prefix}${value}`
			}
		}
	}
</script>
