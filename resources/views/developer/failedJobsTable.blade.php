@extends('layouts.dashboard')

@section('title', __('developer\failedJobs.failedJobs'))

@section('content')
    <div>
        @component('components.datatable')
            @if(isset($filters))
                @slot('filters',$filters)
            @endif
            @isset($deleteButton)
                @slot('deleteButton', $deleteButton)
            @endisset
            @isset($deleteButtonTxt)
                @slot('deleteButtonTxt', $deleteButtonTxt)
            @endisset
            @slot('editWidth',1000)
            <template slot-scope="{object, onDelete}" v-if="object">
                <dynamic-fields :url="`{{Request::url() }}/edit/${object.id}`"></dynamic-fields>
            </template>
        @endcomponent
    </div>
@endsection

