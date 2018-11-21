<template>
	<div>
		<div class="tabs" :class="tabsStyle">
			<ul>
				<li v-for="(tab, index) in tabs" :class="{'is-active': index == selected}" :key="index"
					@click="show(index)">
					<a :href="`#${tab.label}`">
						<font-awesome-icon v-if="tab.icon" :icon="tab.icon"></font-awesome-icon>
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
				tabs: [],
				selected: 0
			}
		},

		mounted() {
			let tab = window.location.href.split('#')[1];
			if(!tab){
				tab = (new URL(window.location.href)).searchParams.get('tab');
			}
			if (tab) {
				this.selected = this.tabs.findIndex((item) => {
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

<style scoped>

</style>