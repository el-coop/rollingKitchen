@extends('layouts.dashboard')

@section('title', $title)

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
			<template #default="{object, onDelete}">
				<dynamic-fields v-if="object" :url="`{{Request::url() }}/edit/${object.id}`"></dynamic-fields>
			</template>
		@endcomponent
	</div>
@endsection
