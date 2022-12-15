@extends('layouts.site')

@section('title',__('kitchen/kitchen.application'))

@section('content')
    @include('kitchen.message')
    <form method="post" action="{{ action('Kitchen\KitchenController@update', $kitchen) }}" ref="form">
        @csrf
        @method('patch')
        <input name="review" type="hidden" value="0" ref="review">
        <tabs :pagination-buttons="true" class="mb-1">
            <tab label="@lang('kitchen/kitchen.businessInformation')">@include('kitchen.kitchen')</tab>
            <tab label="@lang('kitchen/kitchen.kitchenInformation')">@include('kitchen.application')</tab>
            <tab label="@lang('kitchen/kitchen.services')">@include('kitchen.services')</tab>
            @if(!$pastApplications->isEmpty())
                <tab
                    label="@lang('kitchen/kitchen.pastApplications')">@include('kitchen.application.pastApplications')</tab>
            @endif
            <tab label="@lang('kitchen/kitchen.calculator')">@include('kitchen.calculator')</tab>
            <template #buttons>
                <button class="button is-link">
                    @lang('global.save')
                </button>
                <confirmation-submit button-class="is-danger" label="@lang('global.delete')"
                                     title="@lang('kitchen/kitchen.deleteConfirmTitle')"
                                     subtitle="@lang('kitchen/kitchen.deleteConfirmSubtitle')"
                                     yes-text="@lang('global.yes')"
                                     no-text="@lang('global.no')" name="_method" value="delete"></confirmation-submit>
            </template>
        </tabs>
    </form>
{{--    @if($pastApplications->count())--}}
{{--        <div class="box mb-1">--}}
{{--            @include('kitchen.usePastApplication')--}}
{{--        </div>--}}
{{--    @endif--}}
    @if(session()->has('fireworks'))
        <fireworks-modal
            text="{{ str_replace(PHP_EOL,'<br>',app('settings')->get('application_success_modal_' . App::getLocale())) }}"></fireworks-modal>
    @endif
    @include('components.errors')
@endsection
