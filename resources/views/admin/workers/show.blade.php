@extends('layouts.dashboard')

@section('title',$worker->user->name)

@section('content')
	<tabs class="mb-1">
		<tab label="Profile">
			@include('admin.workers.profile')
		</tab>
		<tab label="@lang('worker/worker.shifts')">
			@component('worker.shifts', [
							'shifts' => $futureShifts,
 ])
			@endcomponent
		</tab>
		<tab label="@lang('worker/worker.workedHours')">
			@component('worker.shifts', [
							'shifts' => $pastShifts,
							 'totalHours' => $totalHours
 ])
			@endcomponent
		</tab>
		<tab label="@lang('worker/worker.taxReviews')">
			@include('worker.taxReviews')
		</tab>
	</tabs>
@endsection

