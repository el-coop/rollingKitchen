@extends('layouts.dashboard')

@section('title', __('admin/invoices.invoices'))

@section('content')
	<div>
		@component('components.datatable')

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
					<button class="button is-fullwidth is-success" @click="$bus.$emit('open-payment-modal', object, onUpdate)">
						@lang('admin\invoices.managePayments')
					</button>
				</div>
			</template>
		@endcomponent
	</div>
	<invoice-table :from-url="true">
	</invoice-table>
@endsection
