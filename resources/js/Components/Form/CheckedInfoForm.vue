<template>
    <article class="message is-warning" :class="{'is-hidden' : checked}" >
        <div class="message-header">
            <p v-text="$translations.checkInfo">
            </p>
            <button class="delete" @click="checkInfo" :class="{'is-loading' : loading}" aria-label="delete"></button>
        </div>
    </article>
</template>

<script>
    export default {
        name: "CheckedInfoForm",
        props: {
          action: {
              required: true,
              type: String
          }
        },
        data(){
          return {
              loading: false,
              checked: false
          }
        },
        methods: {
            async checkInfo(){
                this.loading = true;
                let response;
                try {
                    response = await axios['post'](this.action);
                    this.checked = true;
                    this.loading = false;
                    this.$toast.success(this.$translations.infoChecked)
                } catch (e) {
                    response = error.response;
                    if (response.data.errors) {
                        this.formatErrors(response.data.errors);
                    } else {
                        if (response.status === 419) {
                            this.$toast.error(this.$translations.sessionExpired);
                        } else {
                            this.$toast.error(this.$translations.generalError);
                        }
                    }
                    this.loading = false
                }
            },
            formatErrors(errors) {
                this.errors = errors;
                this.$emit('errors', this.errors);
            },
        }
    }
</script>

<style scoped>

</style>