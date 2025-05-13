@extends('layouts.site')

@section('title',$worker->user->name)

@section('content')
    @if($worker->last_submitted != \Carbon\Carbon::now()->year)
        <div class="notification is-warning is-light" >
            @lang('worker/worker.checkInfoMessage')
        </div>
    @endif
	<tabs class="mb-1">
		<tab label="@lang('worker/worker.profile')">@include('worker.profile')</tab>
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
		@if(Auth::user()->user_type == \App\Models\Worker::class && Auth::user()->user->isSupervisor())
			<tab label="@lang('worker/supervisor.manageWorkers')">@include('worker.supervisor.manageWorkplaces')</tab>
		@endif
		@if($worker->taxReviews->count())
			<tab label="@lang('worker/worker.taxReviews')">
				@include('worker.taxReviews')
			</tab>
		@endif
	</tabs>
@endsection
