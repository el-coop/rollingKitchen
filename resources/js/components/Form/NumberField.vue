<template>
	<div class="field">
		<label class="label"  v-text="field.label"></label>
		<div class="control">
			<input class="input" :class="{'is-danger': error}" type="number" step="any" v-model="value" :name="field.name">
		</div>
		<p v-if="error" class="help is-danger" v-text="error[0]"></p>
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