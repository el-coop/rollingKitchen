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
                <dynamic-fields :url="`{{Request::url() }}/show/${object.id}`"></dynamic-fields>
                <div class="mt-1">
                    <ajax-form :action="`{{Request::url() }}/retry/${object.id}`">
                        <button class="button is-info is-fullwidth"
                                type="submit">@lang('developer\failedJobs.retry')</button>
                    </ajax-form>
                </div>
            </template>
        @endcomponent
    </div>
@endsection

