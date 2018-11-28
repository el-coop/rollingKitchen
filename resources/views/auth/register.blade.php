@extends('layouts.site')

@section('title',__('global.register'))

@section('content')
	<div class="section">
		<div class="columns">
			<div class="column is-8">
				@component('components.card',[
					'class' => 'h-100'
				])
					@slot('title')
						<p class="title is-4">
							@lang('global.register')
						</p>
					@endslot
					<form method="post" action="{{ action('Kitchen\KitchenController@store') }}">
						@csrf
						<text-field
								:field="{label: '@lang('auth.kitchenName')',name: 'name', value: '{{ old('name') }}'}"
								:error="{{ $errors->has('name') ? collect($errors->get('name')): 'null'}}"></text-field>
						<text-field
								:field="{label: '@lang('global.email')',name: 'email', subType: 'email', value: '{{ old('email') }}'}"
								:error="{{ $errors->has('email') ? collect($errors->get('email')): 'null'}}"></text-field>
						<select-field :field="{label: '@lang('global.language')',name: 'language', options: {
									@foreach(config('app.locales') as $local)
						{{$local}}: '@lang("global.$local")',
								@endforeach
								}, value: '{{  old('language',App::getLocale()) }}'}"
									  :error="{{ $errors->has('language') ? collect($errors->get('language')): 'null'}}"></select-field>
						<text-field
								:field="{label: '@lang('global.password')',name: 'password', subType: 'password'}"
								:error="{{ $errors->has('password') ? collect($errors->get('password')): 'null'}}"></text-field>
						<text-field
								:field="{label: '@lang('global.password_confirm')',name: 'password_confirmation', subType: 'password'}"></text-field>
						<div class="buttons">
							<button class="button is-primary">
								@lang('global.register')
							</button>
							<a class="button" href="{{ action('Auth\LoginController@login') }}">
								@lang('auth.alreadyHave')
							</a>

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
