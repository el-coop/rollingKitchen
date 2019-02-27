@extends('layouts.site')
@section('title', __('admin/artists.bands'))
@section('content')
    <div>
        @component('components.datatable')
            @slot('customUrl', '\artistManager\bands')
            @slot('deleteButton', true)
            @slot('buttons')
                <button class="button is-light" @click="actions.newObjectForm">@lang('vue.add')</button>
            @endslot
            <template #default="{object, onUpdate}">
                <template v-if="object">
                    <div class="title is-7 has-text-centered">
                            <div>
							<span class="is-size-3"
                                  v-text="object.name || object.name_{{\App::getLocale()}} ||'@lang('admin/artists.createBand')'"></span>
                            </div>
                    </div>
                    <dynamic-form :url="'{{Request::url() }}/edit' + (object.id ? `/${object.id}` : '')"
                                  :on-data-update="onUpdate"
                                  :method="object.id ? 'patch' : 'post'"
                    ></dynamic-form>
                </template>
            </template>
        @endcomponent
    </div>
@endsection