<template>
    <ajax-form method='delete' :action="action" @submitting="buttonClass = 'is-danger is-loading'" @submitted="submitted">
        <confirmation-submit :button-class="buttonClass"
                              :title="$translations.deleteConfirmTitle"
                             subtitle=" "
                             :yes-text="$translations.yes" :no-text="$translations.no"
                             :label="deleteBtn"></confirmation-submit>
    </ajax-form>
</template>
<script>
    import ConfirmationSubmit from '../Global/ConfirmationSubmit';
    import AjaxForm from './AjaxForm';
    export default {
        name: "DeleteForm",
        components: {
            AjaxForm,
            ConfirmationSubmit
        },
        props: {
            action: {
                type: String
            },
            deleteBtn: {
            }

        },
        data() {
            return {
                buttonClass: 'is-danger'
            }
        },
        methods: {
            submitted(response){
                this.buttonClass = 'is-danger';
                this.$emit('delete-submitted', response);
            }
        }
    }
</script>

<style scoped>

</style>
