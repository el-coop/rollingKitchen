@extends('layouts.plain')

@section('body')
    <main>
        <navbar title="@lang('global.title')" :menu="false" title-link="{{ action('HomeController@show') }}"
                class="is-dark" :fluid="false">
            @auth
                @component('components.logout', [
                    'class' => ''
                ])
                @endcomponent
            @else
                <div class="navbar-item has-dropdown is-hoverable">
                    <a class="navbar-link">
                        <figure class="image is-autoX32 is-inline is-hidden-touch">
                            <img src="{{ asset('images/' . App::getLocale() . '.svg') }}">
                        </figure>&nbsp;
                        {{ __('global.' . config('app.locales')[App::getLocale()]) }}
                    </a>

                    <div class="navbar-dropdown">
                        @foreach (config('app.locales') as $language)
                            @if($language != App::getLocale())
                                <a href="{{action ('LocaleController@set', $language) }}"
                                   class="navbar-item">@lang("global.$language")</a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endauth
        </navbar>
        <div class="container">
            @if(Auth::check() && !Auth::user()->checked_info)
                @component('components.checkInformation')
                @endcomponent
            @endif
            @yield('content')
        </div>
    </main>
@endsection
