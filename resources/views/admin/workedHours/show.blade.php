@extends('layouts.dashboard')

@section('title',$title)

@section('content')
    <div class="box">
        <link-date-selector href="{{$downloadAction}}" name="date">
            <a class="button is-info">{{$btn}}</a>
        </link-date-selector>
    </div>
    <dynamic-table :columns="[{
            name: 'column',
            label: '@lang('vue.field')',
            type: 'select',
            options: {{$options}},
            callback: 'numerateOptions'
        }]" :init-fields="{{collect($alreadySelected)}}" :sortable="true" action="{{$addAction}}">
    </dynamic-table>

@endsection
