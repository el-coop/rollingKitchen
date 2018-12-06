<template>
    <ajax-form v-on.stop="" method='delete' :action="action">
        <button type="submit" class="button is-danger">Delete</button>
    </ajax-form>
</template>

<script>
    import AjaxForm from "../../Form/AjaxForm";
    export default {
        components: {
            AjaxForm
        },
        props: {
            rowData: {
                type: Object,
                required: true
            }
        },
        data(){
          return {
              action: ''
          }
        },
        mounted(){
            this.action = window.location.pathname + '/delete/' + this.rowData.id
        }
    }
</script>

<style scoped>

</style>
