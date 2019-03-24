@extends('layouts.dashboard')

@section('title',$title)

@section('content')
	<div>
		@component('components.datatableWithNew')
			@slot('formattersData', $formattersData ?? null)
			@slot('customUrl',$customUrl ?? null)
			@slot('fieldType', $fieldType ?? null)
			@slot('createTitle', $createTitle)
			@slot('withEditLink', $withEditLink ?? true)
			@slot('buttons', $buttons ?? null)
			@slot('extraSlotView', $extraSlotView ?? null)
		@endcomponent
	</div>
@endsection
