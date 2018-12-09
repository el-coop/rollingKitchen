@extends('layouts.dashboard')

@section('title', $title)

@section('content')
	<div>
		@component('components.datatable')
			@isset($deleteButton)
				@slot('deleteButton', $deleteButton)
			@endisset
			@slot('buttons')
				@if(isset($fieldType))
					<a class="button is-light"
					   href="{{ action('Admin\FieldController@index', $fieldType) }}">@lang('admin/kitchens.fields')</a>
				@endif
			@endslot
			@if(isset($filters))
				@slot('filters',$filters)
			@endif
		@endcomponent
	</div>
@endsection
