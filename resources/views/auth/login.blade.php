@extends('layouts.site')

@section('title',__('global.login'))

@section('content')
	<div class="section">
		<div class="columns">
			<div class="column is-half">
				@component('components.card',[
					'class' => 'h-100'
				])
					@slot('title')
						<p class="title is-4 is-title-red">
							@lang('global.login')
						</p>
					@endslot
					<form method="post" action="{{ action('Auth\LoginController@login') }}">
						@csrf
						<text-field
								:field="{label: '@lang('global.email')',name: 'email', subType: 'email'}"
								:error="{{ $errors->count() ? collect([__('auth.failed')]): 'null'}}"></text-field>
						<text-field
								:field="{label: '@lang('global.password')',name: 'password', subType: 'password'}"></text-field>
            <div class="field">
							<label class="checkbox">
								<input type="checkbox" name="remember">
								@lang('auth.rememberMe')
							</label>
						</div>
						<div class="buttons">
							<button class="button is-primary">
								@lang('global.login')
							</button>
							<a href="{{ action('Auth\ForgotPasswordController@showLinkRequestForm') }}"
							   class="button is-dark">@lang('auth.forgot')</a>
							<a href="{{ action('Kitchen\KitchenController@create') }}" class="button">@lang('auth.new')</a>
						</div>
					</form>
				@endcomponent
			</div>

				@include('logoCard')

		</div>
	</div>
@endsection
