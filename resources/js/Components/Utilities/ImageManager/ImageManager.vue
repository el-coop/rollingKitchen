<template>
    <div>
        <slot name="display" :images="images"></slot>
        <ImageList :images="images" :delete-url="deleteUrl" @deleted="removeImage"/>
        <ImageUpload :url="url" :data="data" @uploaded="uploaded"/>
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
            this.$emit('image-uploaded', imageData);
        },

        removeImage(image) {
            const index = this.images.findIndex((item) => {
                return item.id == image.id;
            });
            this.$emit('image-deleted', image);
            this.images.splice(index, 1);
        }
    }
}
</script>
