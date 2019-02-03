<template>
    <div v-if="shift">
        <dynamic-fields :fields="shift">
        </dynamic-fields>
        <dynamic-table :columns="[
        {
            name: 'name',
            label: this.$translations.name,
            type: 'select',
            options: this.workers,
            callback: 'numerateOptions'
        },
        {
            name: 'start-time',
            label: 'start time',
            subType: 'time',
        },
        {
            name: 'end-time',
            label: 'end time',
            subType: 'time',
        }
        ]" :init-fields="shiftWorkers" :action="action">
        </dynamic-table>
    </div>
</template>
<script>
    import DynamicFields from '../Form/DynamicFields';
    import DynamicTable from '../Utilities/DynamicTable';
    export default {
        name: "ManageShift",
        components: {
            DynamicFields,
            DynamicTable
        },
        props: {
            url: {
                type: String,
                required: true
            },
            action: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                shift: null,
                workers: [],
                shiftWorkers: []
            }
        },
        methods: {
            async setUp(){
                const response = await axios.get(this.url);
                this.shiftWorkers = response.data.shiftWorkers;
                this.shift = response.data.shift;
                this.workers = response.data.workers;
            }
        },
        created() {
            this.setUp()
        }
    }
</script>

<style scoped>

</style>