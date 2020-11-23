<template>
    <div>
        <p class="title is-4">
            @lang('kitchen/kitchen.usePastApplication')
        </p>
        <div class="field">
            <label class="label" v-text="label"></label>
            <div class="control">
                <div class="select is-fullwidth">
                    <select v-model="pastApplication">
                        <option v-for="(application, id) in applications" :value="application" v-text="application.year"></option>
                    </select>
                </div>
            </div>
        </div>
        <div class="buttons">
            <button class="button is-fullwidth is-primary"
                    type="button"  v-text="this.$translations.save" @click="useApplication">
            </button>
        </div>

    </div>
</template>

<script>
    export default {
        name: "PastApplicationPicker",
        props: {
            applications: {
                required: true,
                type: Array
            },
            label: {
                required: true,
                type: String
            }
        },
        data(){
            return {
                pastApplication: {}
            }
        },
        methods:{
            useApplication(){
                this.$bus.$emit('use-application', this.pastApplication);
            }
        }
    }
</script>

<style scoped>

</style>