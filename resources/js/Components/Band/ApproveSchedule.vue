<template>
    <div>
        <button @click="reject" class="button is-danger" v-text="this.$translations.reject"></button>
        <button @click="approve" class="button is-info" v-text="this.$translations.approve"></button>
    </div>
</template>

<script>
    import AjaxForm from '../Form/AjaxForm';

    export default {
        name: "ApproveSchedule",
        components: {
            AjaxForm
        },
        props: {
            onUpdate: {
                type: Function,
                required: true
            },
            object: {
                type: Object,
                required: true
            },
            band: {
                type: Number,
                required: true
            }
        },
        methods: {
            async approve() {

                try {
                    let response = await axios.patch(`${this.band}/schedule/${this.object.id}/approve`);
                    this.onUpdate(response.data);
                    this.$toast.success(this.$translations.updateSuccess);
                } catch (error) {
                    this.$toast.error(this.$translations.tryLater, this.$translations.operationFiled);
                }
            },

            async reject() {
                try {
                    let response = await axios.patch(`${this.band}/schedule/${this.object.id}/reject`);
                    this.onUpdate(response.data);
                    this.$toast.success(this.$translations.updateSuccess);
                } catch (error) {
                    this.$toast.error(this.$translations.tryLater, this.$translations.operationFiled);
                }
            }
        }
    }
</script>

<style scoped>

</style>