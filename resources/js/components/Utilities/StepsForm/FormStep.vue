<template>
	<div v-show="active">
		<slot></slot>
	</div>
</template>

<script>
	import PaginationPartialMixin from '../PaginationPartialMixin';

	export default {
		name: "FormStep",
		mixins: [PaginationPartialMixin]

	}
</script>

<style scoped>

</style>