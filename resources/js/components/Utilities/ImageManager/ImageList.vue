<template>
	<div class="list">
		<image-entry v-for="image in images" :image="image" :key="image.id" :delete-url="deleteUrl"
					 @deleted="deleteImage"></image-entry>
	</div>
</template>

<script>
	import ImageEntry from "./ImageEntry";

	export default {
		name: "ImageList",
		components: {ImageEntry},
		props: {
			images: {
				type: Array,
				default() {
					return [];
				}
			},
			deleteUrl: {
				type: String,
				default: ''
			},
		},

		data() {
			return {}
		},

		methods: {
			deleteImage(image) {
				this.$emit('deleted', image);
			}
		}
	}
</script>

