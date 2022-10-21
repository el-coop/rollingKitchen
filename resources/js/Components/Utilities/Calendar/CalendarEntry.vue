<template>
	<div :style="{height: height}" @dblclick="drop(null)">
		<div class="h-100" @drop="drop" @dragenter.prevent @dragover.prevent>
			<h6 class="title is-7" v-text="label"></h6>
			<slot :rawData="raw" :processedData="processedData" :edit="edit"/>
		</div>
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
			height: {
				type: String,
				default: '150px'
			}
		},

		data() {
			return {
				raw: {},
				processedData: {}
			}
		},

		methods: {
			drop(event) {
			    if (event){
                    this.raw = JSON.parse(event.dataTransfer.getData("text/plain"));
                }
				this.$emit('drop', {
					raw: this.raw,
					output: this.output.bind(this)
				});
				this.raw = {};
			},
            edit(payload){
                this.$emit('drop', {
                    raw: payload,
                    output: this.output.bind(this)
                });
                this.raw = {};

            },
			output(data) {
				this.processedData = data;
			},
		}
	}
</script>

<style scoped>

	.title {
		margin-bottom: 0.5rem;
	}
</style>
