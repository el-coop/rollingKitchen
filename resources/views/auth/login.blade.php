@extends('layouts.site')

@section('title',__('misc.login'))

@section('content')
	<div class="section">
		<div class="columns">
			<div class="column is-8">
				@component('components.card',[
					'class' => 'h-100'
				])
					@slot('title')
						<p class="title is-4">
							@lang('misc.login')
						</p>
					@endslot
					<form method="post" action="{{ action('Auth\LoginController@login') }}">
						@csrf
						<text-field
								:field="{label: '@lang('misc.email')',name: 'email', subType: 'email'}"
								:error="{{ $errors->count() ? collect(["The credentials don't match our records"]): 'null'}}"></text-field>
						<text-field
								:field="{label: '@lang('misc.password')',name: 'password', subType: 'password'}"></text-field>
						<div class="buttons">
							<button class="button is-primary">
								@lang('misc.login')
							</button>
							<a href="{{ action('Auth\ForgotPasswordController@showLinkRequestForm') }}"
							   class="button is-dark">I forgot my password</a>
							<a href="{{ action('Kitchen\KitchenController@create') }}" class="button">New Kitchen?</a>
						</div>

					</form>
				@endcomponent
			</div>
			<div class="column">
				@include('logoCard')
			</div>
		</div>
	</div>
@endsection
