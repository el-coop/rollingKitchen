@component('components.nonConfigDatatable', [
    'table' => $shiftsTable,
    'fields' => $shiftsTable['fields'],
    'url' => 'supervisorDatatable'
])
	<template slot-scope="{object, onUpdate}" v-if="object">
		<manage-shift
				:on-update="onUpdate"
				:url="`/worker/shift/${object.id}`"
				:action="`/worker/shift/${object.id}/worker`"
		>

		</manage-shift>
	</template>
@endcomponent
