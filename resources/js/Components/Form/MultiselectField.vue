<template>
	<div class="field">
		<label class="label" v-text="field.label"></label>
		<div class="control">
			<input type="hidden" v-for="(value, index) in selected" :name="`${field.name}[${index}]`" :value="value.id">
			<multiselect v-model="selected" :options="field.options" :multiple="true" :label="field.optionsLabel"
						 :hide-selected="true"
						 track-by="id">
			</multiselect>
			<p v-if="error" class="help is-danger" v-text="errorText"></p>
		</div>
	</div>
</template>

<script>
	import Multiselect from 'vue-multiselect';
	import FieldMixin from './FieldMixin';

	export default {
		components: {
			Multiselect
		},
		name: "MultiselectField",
		mixins: [FieldMixin],

		data() {
			return {
				selected: null
			}
		}
	}
</script>

<style lang="scss">
	@import "~vue-multiselect/dist/vue-multiselect.min.css";
</style>
