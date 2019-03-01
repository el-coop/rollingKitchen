@extends('layouts.dashboard')

@section('title', __('admin/artists.schedule'))

@section('content')
	@component('components.calender')
		@slot('bands', compact('bands'))
		@slot('stages', compact('stages'))
	@endcomponent
@endsection
