@extends('layouts.dashboard')

@section('content')
    <div>
        @component('components.datatable')
            <template slot-scope="{object, onUpdate}" v-if="object">
                <div class="title is-size-3 has-text-centered" v-text="object.name"></div>
                <dynamic-form :url="`/admin/applications/${object.id}`" :on-data-update="onUpdate"></dynamic-form>
            </template>
        @endcomponent
    </div>
@endsection
