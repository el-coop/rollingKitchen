@extends('layouts.dashboard')

@section('title',__('worker/worker.shifts'))

@section('content')
    <div>
        @component('components.datatable')
            @slot('deleteButton', true)
            @slot('formattersData', $formattersData ?? null)
            @slot('buttons')
                <button class="button is-light" @click="actions.newObjectForm">@lang('vue.add')</button>
                <button class="button is-info" @click="$modal.show('worked-hours')">@lang('admin/shifts.exportWorkedHours')</button>
            @endslot
            <template slot-scope="{object, onUpdate}" v-if="object">
                <div class="title is-7 has-text-centered">
                    <div>
                        <span class="is-size-3"
                              v-text="object.name || object.name_{{\App::getLocale()}} ||'@lang('admin/workers.createShift')'"></span>
                    </div>
                </div>
                <dynamic-form :url="'{{Request::url() }}/edit' + (object.id ? `/${object.id}` : '')"
                              :on-data-update="onUpdate"
                              :method="object.id ? 'patch' : 'post'"
                ></dynamic-form>
                @isset($extraSlotView)
                    @include($extraSlotView)
                @endif
            </template>
        @endcomponent
        <export-worked-hours url="{{action('Admin\WorkedHoursExportColumnController@create')}}" :column-options="{{$workedHoursOptions}}" :fields="{{collect($workedHours)}}"></export-worked-hours>
    </div>
@endsection