<template>
    <modal-component  :name="name">
        <slot :object="object"/>
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
        },

        data(){
            return {
                object: null
            }
        },

        mounted(){
            this.$bus.$on('open-edit-modal', (object) => {
                this.object = object;
            })
        }
    }
</script>