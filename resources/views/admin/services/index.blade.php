@extends('layouts.dashboard')


@section('content')

    <div>
        @component('components.datatable')
            @slot('buttons')
                <button class="button is-light" @click="actions.newObjectForm">Add service</button>
            @endslot
            <template slot-scope="{object, onUpdate}" v-if="object">
                <div class="title is-size-3 has-text-centered" v-text="object.name || 'Create service'"></div>
                <dynamic-form :url="'{{Request::url() }}/edit' + (object.id ? `/${object.id}` : '')" :on-data-update="onUpdate"
                :method="object.id ? 'patch' : 'post'"
                ></dynamic-form>
            </template>
        @endcomponent
    </div>

@endsection