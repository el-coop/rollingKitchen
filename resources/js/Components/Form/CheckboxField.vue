<template>
	<div class="field">
		<label class="label" v-text="field.label" v-if="field.label"></label>
		<label class="checkbox" v-for="(option, index) in field.options" :key="index">
			<input type="checkbox" :name="`${field.name}[]`" :value="index" :checked="field.checked || false">
			<span v-text="option"></span>&nbsp;&nbsp;
		</label>
	</div>
</template>

<script>
	import FieldMixin from './FieldMixin';

	export default {
		name: "CheckboxField",
		mixins: [FieldMixin]
	}
</script>
<style scoped>
	.checkbox + .checkbox {
		margin-left: .5em;
	}
</style>