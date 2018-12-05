@extends('layouts.dashboard')

@section('title', $title)

@section('content')
    <div>
        @component('components.datatable')
            @if(isset($filters))
                @slot('filters',$filters)
            @endif
            @slot('editWidth',1000)
                <template slot-scope="{object, onDelete}" v-if="object">
                    <dynamic-form :url="`{{Request::url() }}/edit/${object.id}`" method="delete" success-toast="@lang('developer\errors.resolved')"
                                  :on-data-update="onDelete" button-class="is-danger" button-text="@lang('developer\errors.resolve')"></dynamic-form>
                </template>
        @endcomponent
    </div>
@endsection
