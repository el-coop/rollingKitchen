@extends('layouts.dashboard')

@section('title',__('admin/settings.workedHours'))

@section('content')
    <div class="box">
        <a href="{{action('Admin\WorkedHoursExportColumnController@export')}}"  class="button is-info">@lang('admin/shifts.exportWorkedHours')</a>
    </div>
    <dynamic-table :columns="[{
            name: 'column',
            label: '@lang('vue.field')',
            type: 'select',
            options: {{$workedHoursOptions}},
            callback: 'numerateOptions'
        }, {
            name: 'name',
            label: '@lang('global.name')',
        }]" :init-fields="{{collect($workedHours)}}" :sortable="true" action="{{action('Admin\WorkedHoursExportColumnController@create')}}">
    </dynamic-table>

@endsection
