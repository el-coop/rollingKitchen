@extends('layouts.site')

@section('title',$band->user->name)

@section('content')
	@if(count($pdfs))
		@include('band.message')
	@endif
	<tabs class="mb-1">
		<tab label="@lang('worker/worker.profile')">@include('band.profile')</tab>
		@if($band->payment_method == 'individual')
            <tab @if(Session::exists('bandAdminError'))
                 :start-open="true"
                 @endif
                 label="@lang('admin/bands.admin')">@include('band.admin')</tab>
			<tab label="@lang('band/band.bandMembers')">@include('band.bandMembers')</tab>

		@endif
		<tab label="@lang('admin/artists.schedule')">@include('band.schedules')</tab>
		<tab label="@lang('band/band.setList')">@include('band.setList')</tab>
	</tabs>

@endsection
