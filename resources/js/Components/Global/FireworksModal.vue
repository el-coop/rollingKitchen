<template>
	<modal-component name="fireworks">
		<div class="has-text-centered is-size-4" v-html="text">
		</div>
		<figure class="image is-4by3">
			<img src="/images/welcome.gif">
		</figure>
	</modal-component>
</template>

<script>
	export default {
		name: "fireworks-modal",

		props: {
			text: {
				type: String,
				default: ''
			}
		},

		mounted() {
			this.$modal.show('fireworks');
		}
	}
</script>

<style scoped lang="scss">
	$particles: 50;
	$width: 500;
	$height: 500;

	$box-shadow: ();
	$box-shadow2: ();
	@for $i from 0 through $particles {
		$box-shadow: $box-shadow,
		random($width)-$width / 2 + px random($height)-$height / 1.2 + px hsl(random(360), 100, 50);
		$box-shadow2: $box-shadow2, 0 0 #fff
	}

	.pyro {
		overflow: visible;
		height: 500px;
		max-width: 100%;
		position: relative;
		padding: 1em;
	}

	.pyro::before, .pyro::after {
		content: ' ';
		position: absolute;
		width: 5px;
		height: 5px;
		border-radius: 50%;
		box-shadow: $box-shadow2;
		animation: 1s bang ease-out infinite backwards, 1s gravity ease-in infinite backwards, 5s position linear infinite backwards;
	}

	.pyro::after {
		animation-delay: 1.25s, 1.25s, 1.25s;
		animation-duration: 1.25s, 1.25s, 6.25s;
	}

	@keyframes bang {
		to {
			box-shadow: $box-shadow;
		}

	}

	@keyframes gravity {
		to {
			transform: translateY(200px);
			opacity: 0;
		}
	}

	@keyframes position {
		0%, 19.9% {
			margin-top: 10%;
			margin-left: 40%;
		}
		20%, 39.9% {
			margin-top: 40%;
			margin-left: 30%;
		}
		40%, 59.9% {
			margin-top: 20%;
			margin-left: 70%
		}
		60%, 79.9% {
			margin-top: 30%;
			margin-left: 20%;
		}
		80%, 99.9% {
			margin-top: 30%;
			margin-left: 80%;
		}
	}
</style>
