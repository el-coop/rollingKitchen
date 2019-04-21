@extends('layouts.dashboard')
@section('title', $band->user->name)

@section('content')
    <tabs class="mb-1">
        <tab label="@lang('worker/worker.profile')">@include('admin.bands.band.profile')</tab>
        @if($band->payment_method == 'individual')
            <tab label="@lang('band/band.bandMembers')">@component('admin.bands.band.bandMembers', [
            'bandMembersForDatatable' => $band->bandMembersForDatatable,
            'band' => $band
            ])
                @endcomponent</tab>
            <tab label="@lang('admin/bands.admin')">@component('admin.bands.band.admin',[
				'bandAdmin' => $band->admin
			])
                @endcomponent</tab>
        @endif
        <tab label="@lang('band/band.setList')">@include('band.setList')</tab>
    </tabs>
@endsection
