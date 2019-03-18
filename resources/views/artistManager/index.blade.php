@extends('layouts.site')
@section('title', __('admin/artists.bands'))
@section('content')
	<tabs class="mb-1">
		<tab label="@lang('admin/artists.schedule')">
			<div>
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
				@endcomponent
			</div>
		</tab>
		<tab label="@lang('admin/artists.bands')">
			@component('components.datatableWithNew')
				@slot('customUrl', '\artistManager\bands')
				@slot('createTitle', __('admin/artists.createBand'))
				@slot('withEditLink', false)
			@endcomponent
		</tab>
	</tabs>
@endsection
