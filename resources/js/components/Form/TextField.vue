<template>
	<div class="field">
		<label class="label"  v-text="field.label"></label>
		<div class="control">
			<input class="input" :class="{'is-danger': error}" :type="field.subType || 'text'" v-model="value" :name="field.name" step="any" :readonly="field.readonly">
		</div>
		<p v-if="error" class="help is-danger" v-text="errorText"></p>
	</div>
</template>

<script>
	import FieldMixin from './FieldMixin';
	export default {
		name: "TextField",
		mixins: [FieldMixin]
	}
</script>

<style scoped>

</style>