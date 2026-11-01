<template>
	<div class="list">
		<image-entry v-for="image in images" :image="image" :key="image.id" :delete-url="deleteUrl"
					 :pdf-number="pdfNumber(image)" @deleted="deleteImage"></image-entry>
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
			},

			pdfNumber(image) {
				return this.images.filter((item) => {
					return item.is_pdf;
				}).indexOf(image) + 1;
			}
		}
	}
</script>

