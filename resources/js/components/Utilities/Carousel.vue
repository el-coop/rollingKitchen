<template>
	<div class="card">
		<div v-for="(photo, index) in photos" :key="index" v-show="active == index" class="card-image">
			<figure class="image is-5by3">
				<img :src="photo.url">
			</figure>
		</div>
		<div class="arrow previous" @click="changePhoto(-1)">
			<
		</div>
		<div class="arrow next" @click="changePhoto(+1)">
			>
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
			changePhoto(direction){
				this.active += direction;
				if(this.active < 0){
					this.active = this.photos.length - 1;
				}
				if(this.active > this.photos.length-1){
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
	.next {
		right: 0;
	}
</style>