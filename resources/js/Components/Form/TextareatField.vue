<template>
	<div class="field">
		<label class="label" v-text="field.label"></label>
		<div class="control">
			<textarea class="textarea" :class="{'is-danger': error}" v-model="value" :name="field.name"
					  :disabled="field.readonly" :placeholder="field.placeholder || ''"></textarea>
		</div>
		<p v-if="error" class="help is-danger" v-text="errorText"></p>
	</div>
</template>

<script>
	import FieldMixin from './FieldMixin';

	export default {
		name: "TextareaField",
		mixins: [FieldMixin]

	}
</script>
