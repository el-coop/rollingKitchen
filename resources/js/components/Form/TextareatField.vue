<template>
	<div class="field">
		<label v-text="field.label"></label>
		<div class="control">
			<textarea class="textarea" :class="{'is-danger': error}" v-model="value" :name="field.name"></textarea>
		</div>
		<p v-if="error" class="help is-danger" v-text="error[0]"></p>
	</div>
</template>

<script>
	import FieldMixin from './FieldMixin';

	export default {
		name: "TextareaField",
		mixins: [FieldMixin]

	}
</script>

<style scoped>

</style>