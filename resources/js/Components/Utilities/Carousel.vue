<template>
	<div class="card">
		<div v-for="(photo, index) in photos" :key="index" v-show="active == index" class="card-image">
			<figure class="image is-5by3">
				<a v-if="photo.file.indexOf('.pdf') > 1"
				   :href="photo.url" target="_blank"
				   class="not-image is-flex has-content-justified-center has-items-aligned-center">
					<h4 class="title is-4" v-text="`${$translations.download} PDF`">
					</h4>
				</a>

				<img v-else :src="photo.url">
				<a :href="photo.url" target="_blank" class="button-link">
					<button class="button is-primary">
						<font-awesome-icon icon="external-link-square-alt"></font-awesome-icon>
					</button>
				</a>
			</figure>
		</div>
		<div v-if="Object.keys(photos).length > 0">
			<div class="arrow previous" @click="changePhoto(-1)">
				<div v-if="Object.keys(photos).length > 1">
					<
				</div>
			</div>
			<div class="arrow next" @click="changePhoto(+1)">
				<div v-if="Object.keys(photos).length > 1">
					>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		name: "Carousel",
		props: {
			photos: {
				type: Array,
				required: true
			}
		},

		data() {
			return {
				active: 0
			}
		},

		methods: {
			changePhoto(direction) {
				this.active += direction;
				if (this.active < 0) {
					this.active = this.photos.length - 1;
				}
				if (this.active > this.photos.length - 1) {
					this.active = 0;
				}
			}
		}
	}
</script>

<style scoped lang="scss">
	@import "../../../sass/variables";

	.card {
		position: relative;
		padding-left: 3rem;
		padding-right: 3rem;
	}

	.arrow {
		cursor: pointer;
		position: absolute;
		top: 0;
		height: 100%;
		width: 3rem;
		background-color: black;
		color: white;
		display: flex;
		justify-content: center;
		align-items: center;
		font-size: 1.5rem;

		&:hover {
			color: $cyan;
		}
	}

	.previous {
		left: 0;
	}

	.image {
		position: relative;

		> .not-image {
			height: 100%;
			width: 100%;
			bottom: 0;
			left: 0;
			position: absolute;
			right: 0;
			top: 0;
		}

		> a.button-link {
			opacity: 0.6;
			position: absolute;
			right: 1em;
			bottom: 1em;

			&:hover {
				opacity: 0.8;
			}
		}
	}

	.next {
		right: 0;
	}
</style>
