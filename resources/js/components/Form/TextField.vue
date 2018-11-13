<template>
	<div class="field">
		<label v-text="field.label"></label>
		<div class="control">
			<input class="input" type="text" v-model="value">
		</div>
	</div>
</template>

<script>
	export default {
		name: "TextField",
		props: {
			field: {
				required: true,
				type: Object
			},
		},
		data() {
			return {
				value: this.field.value
			}
		}
	}
</script>

<style scoped>

</style>