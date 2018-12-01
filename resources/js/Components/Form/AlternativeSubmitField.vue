<template>
	<div class="field">
		<input type="hidden" :name="name" :value="value" ref="input">
		<button class="button is-primary is-fullwidth" :class="{'is-loading' : loading}" v-text="field.label"
				type="submit" @mousedown="click"></button>
	</div>
</template>

<script>
	import FieldMixin from './FieldMixin';

	export default {
		name: "AlternativeSubmitField",
		mixins: [FieldMixin],
		props: {
			loading: {
				type: Boolean,
				default: false
			}
		},

		data() {
			return {
				name: '',
				value: '',
			}
		},

		methods: {
			async click() {
				this.name = this.field.name;
				this.value = this.field.value;
			}
		},

		watch: {
			loading(){
				this.name = '';
				this.value = '';
			}
		}
	}
</script>