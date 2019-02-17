@extends('layouts.dashboard')

@section('title',__('admin/workers.workplaces'))

@section('content')

	<div>
		@component('components.datatable')
			@slot('deleteButton', true)
			@slot('buttons')
				<button class="button is-light"
						@click="actions.newObjectForm">@lang('admin/workers.addWorkplace')</button>
			@endslot
			<template v-slot:default="{object, onUpdate}">
				<template v-if="object">
					<div class="title is-size-3 has-text-centered"
						 v-text="object.name || '@lang('admin/workers.createWorkplace')'"></div>
					<dynamic-form :url="'{{Request::url() }}/edit' + (object.id ? `/${object.id}` : '')"
								  :on-data-update="onUpdate"
								  :method="object.id ? 'patch' : 'post'"
								  :hide="['workFunctions']"
					>
						<template v-slot:default="{fields}" v-if="object.id">
							<h4 class="title is-4 mt-1">@lang('worker/supervisor.workFunctions')</h4>
							<dynamic-table v-if="fields.length > 0" :columns="[{
                        name: 'name',
                        label: '@lang('global.name')'
                    },{
                        name: 'payment_per_hour_before_tax',
                        label: '@lang('admin/workers.payment_per_hour_before_tax')',
                        subType: 'number',
                        callback: 'localNumber',

                    }, {
                        name: 'payment_per_hour_after_tax',
                        label: '@lang('admin/workers.payment_per_hour_after_tax')',
                        subType: 'number',
                        callback: 'localNumber',
                    }]" :init-fields="fields[1].value" class="mb-1" :action="`/admin/workplaces/${object.id}`">

							</dynamic-table>
						</template>

					</dynamic-form>
				</template>
			</template>
		@endcomponent
	</div>

@endsection
