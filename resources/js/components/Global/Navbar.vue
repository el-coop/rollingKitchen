<template>
	<nav class="navbar has-shadow">
		<div class="navbar-brand is-hidden-tablet">
			<a class="navbar-item navbar-burger" style="margin-left: unset" @click="openDrawer">
				<span></span>
				<span></span>
				<span></span>
			</a>
		</div>
		<div class="navbar-menu">
			<div class="navbar-start">
				<div class="navbar-item" v-text="title">

				</div>
			</div>
		</div>
	</nav>
</template>

<script>
	export default {
		name: "Navbar",
		props: {
			title: {
				type: String
			}
		},

		methods: {
			openDrawer(){
				this.$bus.$emit('open-drawer');
			}
		}
	}
</script>

<style scoped>

</style>