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
            label: this.$translations.workFunction,
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
        ]" sort-by="startTime" :init-fields="shiftWorkers" :action="shift.closed ? '' : `${this.url}/worker`">
		<template #default="{data}">
			<span>Total hours:</span>&nbsp;
			<span v-text="sumHours(data)"></span>
		</template>
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

		methods: {
			sumHours(data) {
				return data.reduce((total, value) => {
					return total += value.hours
				}, 0);
			}
		}
	}
</script>
