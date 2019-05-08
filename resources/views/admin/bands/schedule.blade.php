@extends('layouts.dashboard')

@section('title', __('admin/artists.schedule'))

@section('content')
	@component('components.calender')
		@slot('bands',$bands)
		@slot('budget',$budget)
		@slot('initBudget',$initBudget)
		@slot('schedules',$schedules)
		@slot('stages', $stages)
		@slot('startDay', $startDay)
		@slot('days', $days)
		@slot('startHour', $startHour)
		@slot('endHour', $endHour)
		@slot('takenTimes', $takenTimes)
	@endcomponent
@endsection
