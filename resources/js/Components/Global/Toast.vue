<template>

</template>
<script>
	export default {
		name: "Toast",

		props: {
			title: {
				type: String,
				required: true
			},

			message: {
				type: String,
				required: true
			},

			type: {
				type: String,
				required: true
			},
            position: {
			    type: String,
                default: 'bottomRight'
            }
		},

		mounted() {
			this.$toast[this.type](this.message, this.title,{'position': this.position});
		}
	}
</script>