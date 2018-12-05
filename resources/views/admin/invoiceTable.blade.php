@extends('layouts.dashboard')

@section('title', __('admin/invoices.invoices'))

@section('content')
	<div>
		@component('components.datatable', ['userUrl' => 'admin'])

			@slot('filters',$filters)
			@slot('editWidth',1000)
			<template slot-scope="{object, onUpdate}" v-if="object">
				<div class="title is-7 has-text-centered">
					<a :href="`{{ action('Admin\ApplicationController@index')  }}/${object.application_id}`">
						<span class="is-size-3" v-text="object.name"></span>
						<font-awesome-icon icon="link"></font-awesome-icon>
					</a>
				</div>
				<dynamic-form :url="`{{Request::url() }}/${object.application_id}/${object.id}`"
							  :on-data-update="onUpdate"></dynamic-form>
				<div class="mt-1">
					<dynamic-form
							:button-text="object.paid ? '@lang('admin/invoices.toggleUnpaid')' : '@lang('admin/invoices.togglePaid')'"
							:init-fields="[]"
							:url="`/admin/invoices/${object.id}/toggle`"
							:button-class="object.paid ? 'is-danger' : 'is-success'"
							:on-data-update="onUpdate">
					</dynamic-form>
				</div>

			</template>
		@endcomponent
	</div>
@endsection
