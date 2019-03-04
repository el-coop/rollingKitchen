@extends('layouts.dashboard')

@section('title', __('admin/artists.schedule'))

@section('content')
	@component('components.calender')
		@slot('bands',$bands)
		@slot('stages', $stages)
	@endcomponent
@endsection
