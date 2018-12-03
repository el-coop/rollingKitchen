<template>
	<div class="field">
		<label class="label" v-text="field.label"></label>
		<div class="control">
			<div class="select is-fullwidth" :class="{'is-danger': error}">
				<select v-model="value" :name="field.name" :disabled="field.readonly">
					<option v-for="(option, val) in field.options" :value="val" v-text="option"></option>
				</select>
			</div>
			<p v-if="error" class="help is-danger" v-text="errorText"></p>
		</div>
	</div>
</template>

<script>
	import FieldMixin from './FieldMixin';

	export default {
		name: "SelectField",
		mixins: [FieldMixin]
	}
</script>
