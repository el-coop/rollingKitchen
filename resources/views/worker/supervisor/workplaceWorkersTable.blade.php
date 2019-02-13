@component('components.nonConfigDatatable', [
    'attribute' => 'workersForSupervisor',
    'fields' => $workersTable['fields'],
    'url' => "/supervisorDatatable/{$workplace->id}"
])
	@slot('exportButton', false)
	@slot('deleteButton', true)
	@slot('buttons')
		<button class="button is-light" @click="actions.newObjectForm">@lang('vue.add')</button>
	@endslot
	@slot('formattersData', collect($formattersData))
	<template slot-scope="{object, onUpdate}" v-if="object">
		<div class="title is-7 has-text-centered">
			<span class="is-size-3" v-text="object.name || '@lang('admin/workers.createWorker')'"></span>
		</div>
		<dynamic-form :url="'workplace/{{$workplace->id}}/worker' + (object.id ? `/${object.id}` : '')"
					  :on-data-update="onUpdate"
					  :method="object.id ? 'patch' : 'post'"
		></dynamic-form>
	</template>
@endcomponent
