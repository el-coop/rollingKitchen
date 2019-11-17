@extends('layouts.dashboard')

@section('title',$title)

@section('content')
    <div class="box is-flex">
        @isset($extraButtons)
            @include($extraButtons)
        @endif
        <component is="{{$withDate ?? false ? 'link-date-selector' : 'a'}}" href="{{$downloadAction}}" name="date">
            <button class="button is-info">{{$btn}}</button>
        </component>
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
