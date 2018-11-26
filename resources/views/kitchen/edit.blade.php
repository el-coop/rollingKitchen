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
		<input name="review" type="hidden" value="0" ref="review">
		<tabs>
			<tab label="Business Information">@include('kitchen.kitchen')</tab>
			<tab label="Kitchen Information">@include('kitchen.application')</tab>
			<tab label="Services">@include('kitchen.services')</tab>
		</tabs>
		<div class="buttons mt-1 has-content-justified-center">
			<button class="button is-link"
					title="Data will be saved for the next time you visit">@lang('global.save')</button>
			@if($application->isOpen())
				<button class="button is-success" type="button" @click="$toast.question('You will not be able to modify your application','Submit for review?',{
				timeout: false, position:'center',buttons: [
					['<button>Yes</button>', (instance, toast) => {
						$refs.review.value = 1;
						$refs.form.submit();
						instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
					}, true],
					['<button>NO</button>', (instance, toast) => {
						instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
					},]
				],})" name="review"
						title="You will not be able to change your data once this is done">@lang('kitchen/kitchen.submit')
				</button>
			@endif
		</div>
	</form>
@endsection