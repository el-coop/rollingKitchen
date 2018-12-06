@extends('layouts.site')

@section('title',__('kitchen/kitchen.application'))

@section('content')
	<div class="notification">
		{!!  str_replace(PHP_EOL,'<br>',$message) !!}
	</div>
	<form method="post" action="{{ action('Kitchen\KitchenController@update', $kitchen) }}" ref="form">
		@csrf
		@method('patch')
		<input name="review" type="hidden" value="0" ref="review">
		<tabs :pagination-buttons="true">
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
				text="{{ str_replace(PHP_EOL,'<br>',app('settings')->get('application_success_modal_' . App::getLocale())) }}"></fireworks-modal>
	@endif
	@include('components.errors')
@endsection
