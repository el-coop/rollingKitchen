<template>
	<div class="drop-field" @dblclick="drop({})">
		<drop class="h-100" @drop="drop">
			<h6 class="title is-7" v-text="label"></h6>
			<slot :rawData="raw" :processedData="processedData" :edit="drop"></slot>
		</drop>
	</div>
</template>

<script>
	export default {
		name: "CalendarEntry",

		props: {
			label: {
				type: String,
				required: true
			},
		},

		data() {
			return {
				raw: {},
				processedData: {}
			}
		},

		methods: {
			drop(payload) {
				this.raw = payload;
				this.$emit('drop', {
					raw: this.raw,
					output: this.output.bind(this)
				});
			},
			output(data) {
				this.processedData = data;
			},
		}
	}
</script>

<style scoped>
	.drop-field {
		height: 100px;
	}

	.title {
		margin-bottom: 0.5rem;
	}
</style>
