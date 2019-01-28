@extends('layouts.site')

@section('title',$worker->user->name)

@section('content')
    <tabs class="mb-1">
        <tab label="@lang('worker/worker.profile')">@include('worker.profile')</tab>
        <tab label="@lang('worker/worker.shifts')"></tab>
        <tab label="@lang('worker/worker.workedHours')"></tab>
        @if(Auth::user()->user_type == \App\Models\Worker::class)
            @if(Auth::user()->user->isSupervisor())
                <tab label="@lang('worker/supervisor.manageWorkers')">@include('worker.supervisor.manageWorkplaces')</tab>
            @endif
        @endif
    </tabs>
@endsection
