<template>
	<nav class="navbar has-shadow">
		<div class="container" :class="{'is-fluid': fluid}">
			<div class="navbar-brand is-hidden-desktop">
				<div class="navbar-item" v-if="menu" @click="openDrawer">
					<button class="button is-inverted">
						<font-awesome-icon icon="bars" class="icon" fixed-width></font-awesome-icon>
					</button>
				</div>
				<a :href="titleLink" class="navbar-item" v-text="title">
				</a>
				<div class="navbar-item ml-auto" v-html="buttons">

				</div>
			</div>
			<div class="navbar-menu">
				<div class="navbar-start">
					<a :href="titleLink" class="navbar-item" v-text="title">

					</a>
				</div>
				<div class="navbar-end" ref="buttons">
					<slot></slot>
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
			},
			menu: {
				type: Boolean,
				default: true
			},

			fluid: {
				type: Boolean,
				default: true
			},

			titleLink: {
				type: String,
				default: '#'
			}
		},

		data() {
			return {
				buttons: ''
			}
		},

		mounted() {
			this.buttons = this.$refs.buttons.innerHTML;
		},

		methods: {
			openDrawer() {
				this.$bus.$emit('open-drawer');
			}
		},


	}
</script>

<style scoped lang="scss">
	@import '../../../sass/variables';

	.navbar {
		margin-bottom: 1rem;

		&.is-dark {
			background-color: lighten($contrast-bg, 30%);
		}
	}

	.navbar-brand {
		> .navbar-item:first-child {
			margin-left: unset;
		}
	}

	.navbar-item.has-dropdown.is-hoverable {
		> .navbar-dropdown {
			display: none;
		}

		&:hover, &:active, &:focus {
			> .navbar-dropdown {
				display: block;
				position: absolute;
				background-color: white;
				border-bottom-left-radius: 6px;
				border-bottom-right-radius: 6px;
				border-top: 2px solid #dbdbdb;
				box-shadow: 0 8px 8px rgba(10, 10, 10, 0.1);
				font-size: 0.875rem;
				left: 0;
				min-width: 100%;
				top: 100%;
				z-index: 20;
			}
		}
	}
</style>