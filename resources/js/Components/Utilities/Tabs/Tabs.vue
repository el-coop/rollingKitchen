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
		</div>
	</div>
</template>

<script>

	export default {
		name: "Tabs",

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