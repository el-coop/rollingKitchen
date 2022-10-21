<template>
    <component :is="tag" draggable="true" @dragstart="dragStart">
        <slot/>
    </component>
</template>

<script>
export default {
    name: "Drag",
    props: {
        tag: {
            type: String,
            default: 'div'
        },
        dataTransfer: {
            type: Object,
        }
    },

    methods: {
        dragStart(event) {
            event.dataTransfer.setData('text/plain',JSON.stringify(this.dataTransfer));
        }
    }
}
</script>

<style scoped>

</style>
