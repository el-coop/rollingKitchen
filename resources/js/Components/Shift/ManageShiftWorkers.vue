<template>
	<dynamic-table v-if="! loading" :columns="[
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
            label: this.$translations.startTime,
            subType: 'time',
        },
        {
            name: 'endTime',
            label: this.$translations.endTime,
            subType: 'time',
        }
        ]" :init-fields="shiftWorkers" :action="shift.closed ? '' : `${this.url}/worker`">
	</dynamic-table>
	<div v-else class="has-text-centered">
		<a class="button is-loading"></a>
	</div>
</template>
<script>
	import DynamicFields from '../Form/DynamicFields';
	import DynamicTable from '../Utilities/DynamicTable';
	import AjaxForm from '../Form/AjaxForm';

	export default {
		name: "ManageShiftWorkers",
		components: {
			DynamicFields,
			DynamicTable,
			AjaxForm,
		},
		props: {
			shift: {
				type: Object,
				required: true
			},

			url: {
				type: String,
				required: true
			},
		},
		data() {
			return {
				loading: true,
				workers: [],
				shiftWorkers: [],
				workFunctions: []
			}
		},
		async created() {
			try {
				const response = await axios.get(this.url);
				this.shiftWorkers = response.data.shiftWorkers;
				this.workFunctions = response.data.workFunctions;
				this.workers = response.data.workers;

			} catch (error) {
				if (response.status === 419) {
					this.$toast.error(this.$translations.sessionExpired);
				} else {
					this.$toast.error(this.$translations.generalError);
				}
			}
			this.loading = false;
		},
	}
</script>
