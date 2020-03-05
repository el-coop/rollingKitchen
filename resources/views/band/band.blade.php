@extends('layouts.site')

@section('title',$band->user->name)

@section('content')
	@if(count($pdfs))
		@include('band.message')
	@endif
	<tabs class="mb-1">
		<tab label="@lang('band/band.information')">@include('band.profile')</tab>
		@if($band->payment_method == 'individual')
			<tab @if(Session::exists('bandAdminError'))
				 :start-open="true"
				 @endif
				 label="@lang('worker/worker.profile')">@include('band.admin')</tab>
			<tab label="@lang('band/band.bandMembers')">@include('band.bandMembers')</tab>

		@endif
		<tab label="@lang('band/band.setList')">@include('band.setList')</tab>
        <tab label="@lang('admin/artists.schedule')">@include('band.schedules')</tab>
    </tabs>

@endsection
