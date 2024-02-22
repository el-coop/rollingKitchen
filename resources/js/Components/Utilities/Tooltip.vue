<template>
    <span :data-tooltip="text">
    <font-awesome-icon icon="info-circle" ></font-awesome-icon>
    </span>
</template>

<script>
export default {
    name: "Tooltip",
    props: {
        text: {
            required: true,
            type: String
        }
    }
}
</script>

<style scoped>

</style>
