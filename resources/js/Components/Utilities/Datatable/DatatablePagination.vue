<template>
	<nav v-if="tablePagination && tablePagination.last_page > 1" :class="['pagination']">
		<a v-if="isOnFirstPage" @click="loadPage('prev')"
           disabled
		   class="pagination-previous"
		   v-html="prevText"
		></a>
        <a v-else @click="loadPage('prev')"
           class="pagination-previous"
           v-html="prevText"
        ></a>
		<a v-if="isOnLastPage" @click="loadPage('next')"
		   disabled
		   class="pagination-next"
		   v-html="nextText"
		></a>
        <a v-else @click="loadPage('next')"
           class="pagination-next"
           v-html="nextText"
        ></a>
		<ul :class="[css.listClass]">
			<template v-if="totalPagesLessThanWindowSize">
				<li v-for="n in totalPage">
					<a v-if="isCurrentPage(n)" @click="loadPage(n)"
					   disabled
					   :class="css.linkClass"
					   v-html="n"
					></a>
                    <a v-else @click="loadPage(n)"
                       :class="css.linkClass"
                       v-html="n"
                    ></a>
				</li>
			</template>
			<template v-else>
				<li>
					<a v-if="isOnFirstPage" @click="loadPage(1)"
					   disabled
					   :class="css.linkClass"
					>1</a>
                    <a v-else @click="loadPage(1)"
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
					<a v-if="isOnLastPage" @click="loadPage(totalPage)"
					   disabled
					   :class="css.linkClass"
					   v-html="totalPage">
					</a>
                    <a v-else @click="loadPage(totalPage)"
                       :class="css.linkClass"
                       v-html="totalPage">
                    </a>
				</li>
			</template>
		</ul>
	</nav>
</template>

<script>
	import PaginationMixin from '../../Vuetable/VuetablePaginationMixin'

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
