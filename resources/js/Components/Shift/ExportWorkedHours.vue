<template>
    <modal-component name="worked-hours">
        <dynamic-table :columns="[{
            name: 'column',
            label: this.$translations.field,
            type: 'select',
            options: this.fields,
            callback: 'numerateOptions'
        }, {
            name: 'name',
            label: this.$translations.name,
        }]" :init-fields="fields" :sortable="true" :action="url">
        </dynamic-table>
    </modal-component>
</template>

<script>
    import ModalComponent from '../Utilities/ModalComponent';
    import DynamicTable from '../Utilities/DynamicTable'

    export default {
        name: "ExportWorkedHours",
        components: {
            ModalComponent,
            DynamicTable
        },
        props: {
            columnOptions: {
                type: Array,
                required: true
            },
            url: {
               type: String,
               required: true
            },

            fields: {
                type: Array,
                required: true
            }
        }
    }
</script>

<style scoped>

</style>