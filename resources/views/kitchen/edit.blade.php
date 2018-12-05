@extends('layouts.site')

@section('title',__('kitchen/kitchen.application'))

@section('content')
	<div class="notification">
		{{ $message }}
	</div>
	<form method="post" action="{{ action('Kitchen\KitchenController@update', $kitchen) }}" ref="form">
		@csrf
		@method('patch')
		<input name="review" type="hidden" value="0" ref="review">
		<tabs>
			<tab label="@lang('kitchen/kitchen.businessInformation')">@include('kitchen.kitchen')</tab>
			<tab label="@lang('kitchen/kitchen.kitchenInformation')">@include('kitchen.application')</tab>
			<tab label="@lang('kitchen/kitchen.services')">@include('kitchen.services')</tab>
			@if(!$pastApplications->isEmpty())
				<tab label="@lang('kitchen/kitchen.pastApplications')">@include('kitchen.application.pastApplications')</tab>
			@endif
		</tabs>
		<div class="buttons mt-1 has-content-justified-center">
			<button class="button is-link">
				@lang('global.save')
			</button>
			@if($application->isOpen())
				<button class="button is-success" type="button"
						@click="$toast.question('@lang('kitchen/kitchen.submitConfirmSubtitle')','@lang('kitchen/kitchen.submitConfirmTitle')',{
				timeout: false, position:'center',buttons: [
					['<button>@lang('global.yes')</button>', (instance, toast) => {
						$refs.review.value = 1;
						$refs.form.submit();
						instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
					}, true],
					['<button>@lang('global.no')</button>', (instance, toast) => {
						instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
					},]
				],})" id="reviewButton">
					@lang('kitchen/kitchen.submitReview')
				</button>
			@endif
		</div>
	</form>
	@if(session()->has('fireworks'))
		<fireworks-modal
				text="{{ app('settings')->get('application_success_modal_' . App::getLocale()) }}"></fireworks-modal>
	@endif
	@if($errors->any())
		@php
			var_dump($errors->all());
		@endphp
		<toast message="@lang('vue.pleaseCorrect')" title="@lang('vue.formErrors')"
			   type="error"></toast>
	@endif
@endsection
