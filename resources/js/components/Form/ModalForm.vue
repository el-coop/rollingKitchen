<template>
    <modal-component  :name="name">
        <slot></slot>
    </modal-component>
</template>

<script>
    export default {
        name: "modal-form",
        props:{
            name: {
                type: String,
                required: true
            },
        }
    }
</script>
