@extends('layouts.dashboard')

@section('title',__('kitchen/kitchen.services'))

@section('content')

    <div>
        @component('components.datatable')
            @slot('buttons')
                <button class="button is-light" @click="actions.newObjectForm">@lang('admin/services.add')</button>
                <a href="{{action('Admin\ServiceController@export')}}" class="button is-info">@lang('admin/services.download_application_services')</a>
            @endslot
            <template slot-scope="{object, onUpdate}" v-if="object">
                <div class="title is-size-3 has-text-centered" v-text="object.name_{{App::getLocale()}} || 'Create service'"></div>
                <dynamic-form :url="'{{Request::url() }}/edit' + (object.id ? `/${object.id}` : '')"
                              :on-data-update="onUpdate"
                              :method="object.id ? 'patch' : 'post'"
                ></dynamic-form>
            </template>
        @endcomponent
    </div>

@endsection
