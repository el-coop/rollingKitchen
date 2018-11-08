<template>
	<nav v-if="tablePagination && tablePagination.last_page > 1" :class="['pagination']">
		<a @click="loadPage('prev')"
		   :disabled="isOnFirstPage"
		   class="pagination-previous"
		   v-html="prevText"
		></a>
		<a @click="loadPage('next')"
		   :disabled="isOnLastPage"
		   class="pagination-next"
		   v-html="nextText"
		></a>
		<ul :class="[css.listClass]">
			<template v-if="totalPagesLessThanWindowSize">
				<li v-for="n in totalPage">
					<a @click="loadPage(n)"
					   :disabled="isCurrentPage(n)"
					   :class="css.linkClass"
					   v-html="n"
					></a>
				</li>
			</template>
			<template v-else>
				<li>
					<a @click="loadPage(1)"
					   :disabled="isOnFirstPage"
					   :class="css.linkClass"
					>1</a>
				</li>
				<li>
					<span :class="css.ellipsisClass">&hellip;</span>
				</li>
				<li v-for="n in windowSize-2">
					<a @click="loadPage(windowStart+n)"
					   :class="[css.linkClass, isCurrentPage(windowStart+n) ? css.activeClass : '']"
					   v-html="windowStart+n">
					</a>
				</li>
				<li>
					<span :class="css.ellipsisClass">&hellip;</span>
				</li>
				<li>
					<a @click="loadPage(totalPage)"
					   :disabled="isOnLastPage"
					   :class="css.linkClass"
					   v-html="totalPage">
					</a>
				</li>
			</template>
		</ul>
	</nav>
</template>

<script>
	import PaginationMixin from 'vuetable-2/src/components/VuetablePaginationMixin'

	export default {
		name: 'DatatablePagination',
		mixins: [PaginationMixin],
		props: {
			onEachSide: {
				type: Number,
				default () {
					return 1
				}
			},
			prevText: {
				type: String,
				default: 'Previous'
			},
			nextText: {
				type: String,
				default: 'Next'
			},
			css: {
				type: Object,
				default() {
					return {
						activeClass: 'is-current',
						listClass: 'pagination-list',
						linkClass: 'pagination-link',
						ellipsisClass: 'pagination-ellipsis'
					}
				}
			}
		},
		computed: {
			totalPagesLessThanWindowSize() {
				return this.totalPage < (this.onEachSide * 2) + 4
			}
		}
	}
</script>