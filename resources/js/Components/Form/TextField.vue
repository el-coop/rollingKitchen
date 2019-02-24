<template>
	<div class="field">
		<label class="label" v-text="field.label"></label>
		<div class="control" :class="{'has-icons-left': field.icon || false}">
			<input class="input" :class="{'is-danger': error}" :type="field.subType || 'text'" v-model="value"
				   @keypress.enter.prevent
				   :required="field.required || false"
				   :name="field.name" step="any" :disabled="field.readonly" :placeholder="field.placeholder || ''">
			<span class="icon is-small is-left" v-if="field.icon || false">
			<font-awesome-icon :icon="field.icon" size="sm"></font-awesome-icon>
			</span>
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
