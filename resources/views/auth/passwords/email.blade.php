@extends('layouts.site')

@section('title',__('auth.resetPassword'));

@section('content')
	<div class="section">
		<div class="columns">
			<div class="column is-8">
				@component('components.card',[
					'class' => 'h-100'
				])
					@slot('title')
						<p class="title is-4">
							@lang('auth.resetPassword')
						</p>
					@endslot
					@if (session('status'))
						<div class="notification" role="alert">
							{{ session('status') }}
						</div>
					@endif

					<form method="post" action="{{ action('Auth\ForgotPasswordController@sendResetLinkEmail') }}">
						@csrf
						<text-field
								:field="{label: '@lang('global.email')',name: 'email', subType: 'email', value: '{{ old('email') }}'}"
								:error="{{ $errors->has('email') ? collect($errors->get('email')): 'null'}}"></text-field>

						<div class="buttons">
							<button class="button is-primary">
								@lang('auth.sendResetPasswordLink')
							</button>
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
