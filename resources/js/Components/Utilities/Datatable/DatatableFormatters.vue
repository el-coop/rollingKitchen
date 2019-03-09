<script>
	export default {
		methods: {
			numToBoolTag(value) {
				if (value > 0) {
					return `<span class="tag is-success">${this.$translations.yes}</span>`;
				}
				return `<span class="tag is-danger">${this.$translations.no}</span>`;
			},
			boolean(value) {
				if (value) {
					return `<span class="tag is-success">${this.$translations.yes}</span>`;
				}
				return `<span class="tag is-danger">${this.$translations.no}</span>`;
			},

			translate(value) {
				return this.$translations[value];
			},

			localNumber(value) {
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
			numerateOptions(value, {options}) {
				return options[value];
			},
			prefix(value, {callbackOptions}) {
				return `${callbackOptions.prefix}${value}`
			},
			date(value) {
				const date = new Date(value);
				let year = date.getUTCFullYear();

				let month = (1 + date.getUTCMonth()).toString();
				month = month.length > 1 ? month : '0' + month;

				let day = date.getUTCDate().toString();
				day = day.length > 1 ? day : '0' + day;

				return day + '/' + month + '/' + year;
			},

			dataCompleted(value) {
				let percent = Math.round(100 * value / this.formattersData.totalDataCount);
				if (percent > 100) {
					percent = 100;
				}
				return `${percent}%`;
			}
		}
	}
</script>
