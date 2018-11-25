<template>
	<div>
		<image-list :images="images" :delete-url="deleteUrl" @deleted="removeImage"></image-list>
		<image-upload :url="url" :data="data" @uploaded="uploaded"></image-upload>
	</div>
</template>

<script>
	import ImageList from './ImageList'
	import ImageUpload from "./ImageUpload";

	export default {
		name: "ImageManager",
		components: {
			ImageUpload,
			ImageList
		},

		props: {
			initImages: {
				type: Array,
				default() {
					return [];
				}
			},
			url: {
				type: String,
				required: true
			},

			deleteUrl: {
				type: String,
				default: false
			},
			data: {
				type: Object,
				default() {
					return {};
				}
			},
		},

		data() {
			return {
				images: this.initImages
			}
		},

		methods: {
			uploaded(imageData) {
				this.images.push(imageData);
			},

			removeImage(image) {
				const index = this.images.findIndex((item) => {
					return item.id == image.id;
				});
				this.images.splice(index, 1);
			}
		}
	}
</script>

<style scoped>
	.image.is-max37-max64 {
		width: auto;
		height: auto;
		max-width: 64px;
		max-height: 37px;
	}
</style>