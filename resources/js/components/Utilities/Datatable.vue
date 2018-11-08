<template>
	<div>
		<div class="table-wrapper">
			<div class="table-parent">
				<div class="table-container">
					<vuetable ref="table"
							  pagination-path=""
							  :api-url="url"
							  :fields="fields"
							  :css="css"
							  :append-params="params"
							  :per-page="perPage"
							  @vuetable:pagination-data="paginationData"
							  @vuetable:loading='tableLoading'
							  @vuetable:loaded='tableLoaded'>
					</vuetable>
				</div>
				<div class="level">
					<div class="level-left">
						<vuetable-pagination-info class="level-item" ref="paginationInfo">
						</vuetable-pagination-info>
					</div>
					<div class="level-right">
						<vuetable-pagination ref="pagination" class="level-item"
											 @vuetable-pagination:change-page="changePage"></vuetable-pagination>
					</div>
				</div>
			</div>
			<div class="filter">
				<datatable-filter :table-fields="fields" @filter="filter"></datatable-filter>
			</div>
		</div>
	</div>
</template>

<script>
	import Vuetable from 'vuetable-2/src/components/Vuetable.vue';
	import VuetablePaginationInfo from 'vuetable-2/src/components/VuetablePaginationInfo.vue';
	import VuetablePagination from './DatatablePagination';
	import DatatableFilter from "./DatatableFilter";

	export default {
		components: {
			DatatableFilter,
			Vuetable,
			VuetablePaginationInfo,
			VuetablePagination
		},
		props: {
			url: {
				required: true,
				type: String
			},
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
			extraParams: {
				type: Object,
				default() {
					return {};
				}
			},
		},

		data() {
			return {
				loading: false,
				tableCss: 'table is-bordered is-striped is-fullwidth',
				css: {
					tableClass: this.tableCss,
					ascendingClass: 'column-sorted column-sorted-up',
					descendingClass: 'column-sorted column-sorted-down',
				},
				perPage: 20,
				params: this.extraParams
			}
		},

		computed: {},

		mounted() {
		},

		methods: {
			paginationData(paginationData) {
				this.$refs.pagination.setPaginationData(paginationData);
				this.$refs.paginationInfo.setPaginationData(paginationData);
			},
			tableLoading() {
				this.css.tableClass = `${this.tableCss} is-loading`;
			},
			tableLoaded() {
				this.css.tableClass = this.tableCss;
			},

			changePage(page) {
				this.$refs.table.changePage(page);
			},
			filter(filters) {
				this.params.filter = filters;
				Vue.nextTick(() => {
					this.$refs.table.refresh()
				})
			}
		},
	}
</script>

<style lang="scss">
	@import "../../../sass/variables";

	.table.is-loading {
		opacity: 0.4;
		position: relative;
		transition: opacity .3s ease-in-out;
	}

	.table.is-loading:after {
		position: absolute;
		content: '';
		top: 40%;
		left: 50%;
		margin: -30px 0 0 -30px;
		border-radius: 100%;
		animation-fill-mode: both;
		border: 4px solid #000;
		height: 60px;
		width: 60px;
		background: transparent !important;
		display: inline-block;
		animation: pulse 1s 0s ease-in-out infinite;
	}

	@keyframes pulse {
		0% {
			-webkit-transform: scale(0.6);
			transform: scale(0.6);
		}
		50% {
			-webkit-transform: scale(1);
			transform: scale(1);
			border-width: 12px;
		}
		100% {
			-webkit-transform: scale(0.6);
			transform: scale(0.6);
		}
	}

	.column-sorted:after {
		float: right;
	}

	.column-sorted.column-sorted-down:after {
		content: "\25bc"
	}

	.column-sorted.column-sorted-up:after {
		content: '\25b2'
	}

	.table-wrapper {
		display: flex;
		flex-direction: column-reverse;

		> .table-parent {
			flex: 1;
			margin-top: 1rem;
			> .table-container {
				max-width: calc(100vw - 2.5rem);
			}

			@media #{$above-tablet}{
				margin-top: 0;
				margin-right: 1rem;
			}
		}

		@media #{$above-tablet}{
			flex-direction: row;
		}
	}
</style>