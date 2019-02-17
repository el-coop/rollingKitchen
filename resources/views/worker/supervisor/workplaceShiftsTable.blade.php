@component('components.nonConfigDatatable', [
    'attribute' => 'shiftsForSupervisor',
    'fields' => $shiftsTable['fields'],
    'url' => "/supervisorDatatable/{$workplace->id}"
])
	@slot('exportButton', false)
	<template #default="{object, onUpdate}">
		<template v-if="object">

			<dynamic-fields :fields="[{
			name: 'date',
			label: '@lang('admin/shifts.date')',
			type: 'text',
			subType: 'date',
			value: object.date,
			readonly: true
		}, {
			name: 'workplace',
			label: '@lang('worker/worker.workplace')',
			type: 'text',
			value: '{{ $workplace->name }}',
			readonly: true
		}, {
			name: 'hours',
			label: '@lang('admin/shifts.hours')',
			type: 'text',
			value: object.hours,
			readonly: true
		}]">
			</dynamic-fields>
			<div class="mt-1" v-if="!object.closed">
				<ajax-form method="patch" :action="`/worker/shift/${object.id}`"
						   @submitted="$event.status === 200 ? onUpdate($event.data) : ''">
					<button class="button is-danger is-fullwidth" type="submit"
							v-text="$translations.closeShift"></button>
				</ajax-form>
			</div>

			<h4 class="title is-4 mt-1">
				@lang('worker/supervisor.crew'):
			</h4>
			<manage-shift-workers
					:shift="object"
					:url="`/worker/shift/${object.id}`">
			</manage-shift-workers>
		</template>
	</template>
@endcomponent
