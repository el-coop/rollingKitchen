<template>
	<div class="field">
		<label v-text="field.label"></label>
		<div class="control">
			<textarea class="textarea" v-model="value"></textarea>
		</div>
	</div>
</template>

<script>
	export default {
		name: "TextareaField",
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