@extends('layouts.dashboard')

@section('title',__('admin/workers.workplaces'))

@section('content')

    <div>
        @component('components.datatable')
            @slot('deleteButton', true)
            @slot('buttons')
                <button class="button is-light" @click="actions.newObjectForm">@lang('admin/workers.addWorkplace')</button>
            @endslot
            <template slot-scope="{object, onUpdate}" v-if="object">
                <div class="title is-size-3 has-text-centered" v-text="object.name || '@lang('admin/workers.createWorkplace')'"></div>
                <dynamic-form :url="'{{Request::url() }}/edit' + (object.id ? `/${object.id}` : '')"
                              :on-data-update="onUpdate"
                              :method="object.id ? 'patch' : 'post'"
                              :hide="['workFunctions']"
                >
                    <template slot-scope="{fields}">
                        <dynamic-table v-if="fields" :columns="[{
                        name: 'name',
                        label: '@lang('global.name')'
                    },{
                        name: 'payment_per_hour_before_tax',
                        label: '@lang('admin/workers.payment_per_hour_before_tax')',
                        subType: 'number',
                        callback: 'localNumber',

                    }, {
                        name: 'payment_per_hour_after_tax',
                        label: '@lang('admin/workers.payment_per_hour_after_tax')',
                        subType: 'number',
                        callback: 'localNumber',
                    }]" :init-fields="fields[1].value" class="mb-1" :action="`/admin/workplaces/${object.id}`">
                        </dynamic-table>
                    </template>

                </dynamic-form>
            </template>
        @endcomponent
    </div>

@endsection
