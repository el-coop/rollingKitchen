@extends('layouts.plain')

@section('body')
	<main>
		<navbar title="@lang('global.title')" :menu="false" class="is-dark" :fluid="false">
			<div class="navbar-item has-dropdown is-hoverable">
				<a class="navbar-link">
					{{ __('misc.' . config('app.locales')[App::getLocale()]) }}
				</a>

				<div class="navbar-dropdown">
					@foreach (config('app.locales') as $language)
						<a href="{{action ('LocaleController@set', $language) }}"
						   class="navbar-item">@lang("misc.$language")</a>
					@endforeach
				</div>
			</div>
		</navbar>
		<div class="container">
			@yield('content')
		</div>
	</main>
@endsection
