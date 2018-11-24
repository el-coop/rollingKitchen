@extends('layouts.site')

@section('title',__('kitchen/kitchen.application'))

@section('content')
	<form method="post" action="{{ action('Kitchen\KitchenController@update', $kitchen) }}">
		@csrf
		<tabs>
			<tab label="Business Information">@include('kitchen.kitchen')</tab>
			<tab label="Services">@include('kitchen.services')</tab>
			<tab label="Kitchen Information">@include('kitchen.application')</tab>
		</tabs>
		<div class="buttons mt-1 has-content-justified-center">
			<button class="button is-link"
					title="Data will be saved for the next time you visit">@lang('global.save')</button>
			<button class="button is-success"
					title="You will not be able to change your data once this is done">@lang('kitchen/kitchen.submit')
			</button>
		</div>
	</form>
@endsection