<script>
	export default {
		name: "PaginationPartialMixin",
		props: {
			label: {
				type: String,
				required: true
			},
		},

		created() {
			this.$parent.views.push(this);
		},

		data() {
			return {
				active: false
			}
		},

		mounted() {
			this.active = this.$parent.selected == this.index;
		},

		computed: {
			index() {
				return this.$parent.views.indexOf(this)
			}
		},

		watch: {
			'$parent.selected'(index) {
				this.active = index == this.index;
			}
		}
	}
</script>
