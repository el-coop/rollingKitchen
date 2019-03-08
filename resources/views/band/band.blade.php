@extends('layouts.site')

@section('title',$band->user->name)

@section('content')
    <tabs class="mb-1">
        <tab label="@lang('band/band.paymentMethod')">@include('band.paymentMethod')</tab>
        <tab label="@lang('worker/worker.profile')">@include('band.profile')</tab>
    </tabs>
@endsection