@extends('layouts.site')

@section('title',__('kitchen/kitchen.application'))

@section('content')
    @include('kitchen.message')
    <form method="post" action="{{ action('Kitchen\KitchenController@update', $kitchen) }}" ref="form">
        @csrf
        @method('patch')
        <input name="review" type="hidden" value="0" ref="review">
        @if($application->showsExtraInfo())
        <toggle-chooser>
            <select-view label="@lang('kitchen/extraInfo.title')">
                <tabs :pagination-buttons="true" class="mb-1">
                    <tab label="@lang('kitchen/extraInfo.dimensionsTab')">@include('kitchen.extraInfo.dimensions')</tab>
                    <tab label="@lang('kitchen/extraInfo.equipmentTab')">@include('kitchen.extraInfo.equipment')</tab>
                    <tab label="@lang('kitchen/extraInfo.menuTab')">@include('kitchen.extraInfo.menu')</tab>
                    <tab label="@lang('kitchen/extraInfo.otherTab')">@include('kitchen.extraInfo.other')</tab>
                    <template #buttons>
                        @include('kitchen.buttons')
                    </template>
                </tabs>
            </select-view>
            <select-view label="@lang('kitchen/kitchen.application')">
        @endif
        <tabs :pagination-buttons="true" class="mb-1">
            <tab label="@lang('kitchen/kitchen.businessInformation')">@include('kitchen.kitchen')</tab>
            <tab label="@lang('kitchen/kitchen.kitchenInformation')">@include('kitchen.application')</tab>
            <tab label="@lang('kitchen/products.menuTab')">@include('kitchen.application.products')</tab>
            <tab label="@lang('kitchen/kitchen.services')">@include('kitchen.services')</tab>
            @if(!$pastApplications->isEmpty())
                <tab
                    label="@lang('kitchen/kitchen.pastApplications')">@include('kitchen.application.pastApplications')</tab>
            @endif
            <tab label="@lang('kitchen/kitchen.calculator')">@include('kitchen.calculator')</tab>
            <template #buttons>
                @include('kitchen.buttons')
            </template>
        </tabs>
        @if($application->showsExtraInfo())
            </select-view>
        </toggle-chooser>
        @endif
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
