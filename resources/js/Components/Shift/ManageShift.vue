<template>
    <div v-if="shift">
        <dynamic-fields :fields="shift" :hide="['closed']">
        </dynamic-fields>
        <dynamic-table :columns="[
        {
            name: 'worker',
            label: this.$translations.name,
            type: 'select',
            options: this.workers,
            callback: 'numerateOptions'
        },
        {
            name: 'workFunction',
            label: this.$translations.workFunctions,
            type: 'select',
            options: this.workFunctions,
            callback: 'numerateOptions'
        },
        {
            name: 'startTime',
            label: 'start time',
            subType: 'time',
        },
        {
            name: 'endTime',
            label: 'end time',
            subType: 'time',
        }
        ]" :init-fields="shiftWorkers" :action="tableAction">
        </dynamic-table>
        <div class="mt-1" v-if="!shift[3].value">
            <ajax-form method="patch" :action="url">
                <button class="button is-danger" type="submit" v-text="$translations.closeShift"></button>
            </ajax-form>
        </div>
    </div>
</template>
<script>
    import DynamicFields from '../Form/DynamicFields';
    import DynamicTable from '../Utilities/DynamicTable';
    import AjaxForm from '../Form/AjaxForm';
    export default {
        name: "ManageShift",
        components: {
            DynamicFields,
            DynamicTable,
            AjaxForm,
        },
        props: {
            url: {
                type: String,
                required: true
            },
            action: {
                type: String,
                required: true
            },
        },
        data() {
            return {
                shift: null,
                workers: [],
                shiftWorkers: [],
                workFunctions: []
            }
        },
        methods: {
            async setUp(){
                const response = await axios.get(this.url);
                this.shiftWorkers = response.data.shiftWorkers;
                this.shift = response.data.shift;
                this.workers = response.data.workers;
                this.workFunctions = response.data.workFunctions;

            }
        },
        created() {
            this.setUp()
        },
        computed: {
            tableAction: function () {
                if ( this.shift[3].value === true){
                    return '';
                }
                return this.action;
            }
        }
    }
</script>

<style scoped>

</style>