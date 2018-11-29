<template>
	<div v-if="active">
		<slot></slot>
	</div>
</template>

<script>
	import PaginationPartialMixin from '../PaginationPartialMixin';

	export default {
		name: "SelectView",
		mixins: [PaginationPartialMixin]

	}
</script>

<style scoped>

</style>