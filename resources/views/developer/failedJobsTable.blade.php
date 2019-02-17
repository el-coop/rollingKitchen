@extends('layouts.dashboard')

@section('title', __('developer/failedJobs.failedJobs'))

@section('content')
	<div>
		@component('components.datatable')
			@if(isset($filters))
				@slot('filters',$filters)
			@endif
			@isset($deleteButton)
				@slot('deleteButton', $deleteButton)
			@endisset
			@isset($deleteButtonTxt)
				@slot('deleteButtonTxt', $deleteButtonTxt)
			@endisset
			@slot('editWidth',1000)
			<template v-slot:default="{object, onDelete}">
				<dynamic-form v-if="object"
							  :url="`{{Request::url() }}/retry/${object.id}`"
							  method="post"
							  button-text="@lang('developer/failedJobs.retry')"
							  button-class="is-info"
							  :on-data-update="onDelete"
							  success-toast="@lang('developer/failedJobs.retried')"
				></dynamic-form>
			</template>
		@endcomponent
	</div>
@endsection

