@extends('layouts.site')

@section('title',$band->user->name)

@section('content')
    <tabs class="mb-1">
        <tab label="@lang('worker/worker.profile')">@include('band.profile')</tab>
        @if($band->payment_method == 'individual')
            <tab label="@lang('band/band.bandMembers')">@include('band.bandMembers')</tab>
        @endif
    </tabs>
@endsection