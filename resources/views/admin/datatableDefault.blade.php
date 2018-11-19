@extends('layouts.dashboard')

@section('content')
	<div>
		@component('components.datatable')
			@slot('buttons')
				@if(isset($fieldType))
					<a class="button is-light"
					   href="{{ action('Admin\FieldController@index', $fieldType) }}">@lang('admin/kitchens.fields')</a>
				@endif
			@endslot
		@endcomponent
	</div>
@endsection
