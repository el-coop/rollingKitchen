<template>
	<div class="container is-fluid">
		<vuetable ref="table"
				  :fields="fields"
				  :css="css"
				  :per-page="perPage">
		</vuetable>
		<!--<div class="row text-center text-md-left">-->
		<!--<div class="col-12" :class="{ 'col-md-6' : inlineForms}">-->
		<!--<vuetable-pagination-info ref="paginationInfo"-->
		<!--:info-template="translations.infoTemplate"-->
		<!--:no-data-template="translations.infoNoData"-->
		<!--:css="{-->
		<!--'infoClass' : 'pb-3'-->
		<!--}">-->
		<!--</vuetable-pagination-info>-->
		<!--</div>-->
		<!--<div class="col-12 d-flex d-md-block" :class="{ 'col-md-6' : inlineForms}">-->
		<!--<vuetable-pagination ref="pagination"-->
		<!--:onEachSide="1"-->
		<!--:css="cssPagination"-->
		<!--@vuetable-pagination:change-page="changePage">-->
		<!--</vuetable-pagination>-->
		<!--</div>-->
		<!--</div>-->
	</div>
</template>

<script>

	import Vuetable from 'vuetable-2/src/components/Vuetable.vue';

	export default {
		components: {
			Vuetable
		},
		props: {
			fields: {
				type: Array,
				default() {
					return [];
				}
			},
			perPageOptions: {
				type: Array,
				default() {
					return [10, 20, 50, 100];
				}
			},
		},

		data() {
			return {
				loading: false,
				css: {
					tableClass: 'table is-bordered is-striped is-fullwidth',
					ascendingClass: 'column-sorted column-sorted-up',
					descendingClass: 'column-sorted column-sorted-down',
				},
				perPage: 20
			}
		},

		computed: {},

		mounted() {
		},

		methods: {},
	}
</script>

<style lang="scss">
	.column-sorted:after {
		float: right;
	}

	.column-sorted.column-sorted-down:after {
		content: "\25bc"
	}

	.column-sorted.column-sorted-up:after {
		content: '\25b2'
	}
</style>