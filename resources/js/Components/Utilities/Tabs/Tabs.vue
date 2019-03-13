<template>
	<div>
		<div class="tabs" :class="tabsStyle">
			<ul>
				<li v-for="(tab, index) in views" :class="{'is-active': index == selected}" :key="index"
					@click="show(index)">
					<a>
						<font-awesome-icon v-if="tab.icon" :icon="tab.icon" class="icon"></font-awesome-icon>
						<span v-text="tab.label"></span>
					</a>
				</li>
			</ul>
		</div>
		<div class="box">
			<slot></slot>
			<div class="buttons mt-1 has-content-justified-center" v-if="paginationButtons">
				<button class="button is-dark" @click="showPrev()" v-if="selected > 0"
						type="button" v-html="$translations.previous">
				</button>
				<button class="button is-dark" @click="showNext()" v-if="selected !== views.length-1"
						type="button" v-html="$translations.next">
				</button>
				<slot name="buttons"></slot>
			</div>
		</div>
	</div>
</template>

<script>

	export default {
		name: "Tabs",

		props: {
			paginationButtons: {
				type: Boolean,
				default: false
			}
		},

		data() {
			return {
				views: [],
				selected: 0
			}
		},

		mounted() {
			let tab = window.location.href.split('#')[1];
			if (!tab) {
				tab = (new URL(window.location.href)).searchParams.get('tab');
			}
			if (tab) {
				this.selected = this.views.findIndex((item) => {
					return item.label === tab;
				}) || 0;
			}
		},

		methods: {
			show(tab) {
				this.selected = tab;
			},

			showPrev() {
				this.selected--;
				if (this.selected < 0) {
					this.selected = this.views.length - 1;
				}
			},

			showNext() {
				this.selected++;
				if (this.selected === this.views.length) {
					this.selected = 0;
				}
			}
		},

		computed: {
			tabsStyle() {
				if (window.matchMedia("(min-width: 768px)").matches) {
					return []
				}

				return [
					'is-fullwidth',
					'is-toggle'
				]
			}
		}
	}
</script>

<style scoped lang="scss">
	@media screen and (max-width: 768px) {
		.tabs ul {
			flex-direction: column;

			li {
				width: 100%;
			}
		}
	}
</style>
