@extends('layouts.dashboard')

@section('title',$title)

@section('content')
    <div>
        @component('components.datatable')
            @slot('deleteButton', true)
            @slot('buttons')
                <button class="button is-light" @click="actions.newObjectForm">@lang('vue.add')</button>
                @isset($buttons)
                    @foreach($buttons as $button)
                        {{$button}}
                @endisset
            @endslot
            <template slot-scope="{object, onUpdate}" v-if="object">
                <div class="title is-7 has-text-centered">
                    <component :is="object.id ? 'a' : 'div'"
                               :href="object.id ? `{{Request::url() }}/${object.id}` : '#'">
                        <span class="is-size-3" v-text="object.name || '{{ $createTitle }}'"></span>
                        <font-awesome-icon icon="link" v-if="object.id"></font-awesome-icon>
                    </component>
                </div>
                <dynamic-form :url="'{{Request::url() }}/edit' + (object.id ? `/${object.id}` : '')"
                              :on-data-update="onUpdate"
                              :method="object.id ? 'patch' : 'post'"
                ></dynamic-form>
            </template>
        @endcomponent
    </div>
@endsection
