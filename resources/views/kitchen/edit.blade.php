@extends('layouts.site')

@section('title',__('kitchen/kitchen.application'))

@section('content')
	@if(!$application->isOpen())
		<div class="notification">
			{{ $message }}
		</div>
	@endif
	<form method="post" action="{{ action('Kitchen\KitchenController@update', $kitchen) }}" ref="form">
		@csrf
		@method('patch')
		<input name="review" type="hidden" value="0" ref="review">
		<tabs>
			<tab label="@lang('kitchen/kitchen.businessInformation')">@include('kitchen.kitchen')</tab>
			<tab label="@lang('kitchen/kitchen.kitchenInformation')">@include('kitchen.application')</tab>
			<tab label="@lang('kitchen/kitchen.services')">@include('kitchen.services')</tab>
		</tabs>
		<div class="buttons mt-1 has-content-justified-center">
			<button class="button is-link">
				@lang('global.save')
			</button>
			@if($application->isOpen())
				<button class="button is-success" type="button" @click="$toast.question('@lang('kitchen/kitchen.submitConfirmSubtitle')','@lang('kitchen/kitchen.submitConfirmTitle')',{
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
@endsection