<template>
	<div class="field">
		<p v-if="error" class="help is-danger" v-text="errorText"></p>
	</div>
</template>

<script>
	import FieldMixin from './FieldMixin';

	export default {
		name: "HelpField",
		mixins: [FieldMixin]
	}
</script>

