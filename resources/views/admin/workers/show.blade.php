@extends('layouts.dashboard')

@section('title',$worker->user->name)

@section('content')
	<tabs class="mb-1">
		<tab label="Profile">
			@include('admin.workers.profile')
		</tab>
		@if($futureShifts->count())
			<tab label="@lang('worker/worker.shifts')">
				@component('worker.shifts', [
								'shifts' => $futureShifts,
	 ])
				@endcomponent
			</tab>
		@endif
		@if($pastShifts->count())
			<tab label="@lang('worker/worker.workedHours')">
				@component('worker.shifts', [
								'shifts' => $pastShifts,
								 'totalHours' => $totalHours
	 ])
				@endcomponent
			</tab>
		@endif
		<tab label="@lang('worker/worker.taxReviews')">
			@include('worker.taxReviews')
		</tab>
	</tabs>
@endsection

