<template>
    <ajax-form method="post" @errors="handleErrors" @submitting="submitting = true" :action="url"
               @submitted="submitted">
        <div class="buttons">
            <button class="button is-fullwidth is-info mt-2" :class="{'is-loading' : submitting}"
                    type="submit" v-text="$translations.sendUpdateEmailButton">
            </button>
        </div>
    </ajax-form>
</template>

<script>
import AjaxForm from "../Form/AjaxForm";

export default {
    name: "SendWorkerUpdateInfoEmail",
    components: {AjaxForm},
    props: {
        url: {
            type: String,
            required: true
        }
    },

    data() {
        return {
            submitting: false,
        }
    },

    methods: {

        handleErrors(errors) {
            this.errors = errors;
            if (Object.keys(this.errors).length) {
                this.$toast.error(this.$translations.pleaseCorrect, this.$translations.formErrors);
            }
        },

        submitted(response) {
            this.submitting = false;
            this.alternativeSubmitting = false;
            if (response.status === 200 || response.status === 201) {
                this.$toast.success(this.$translations.emailSent);
            }
        },
    }
}
</script>

<style scoped>

</style>
