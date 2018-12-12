@extends('layouts.site')

@section('title',__('global.welcome'))


@section('content')

    <section class="section">

        <div class="columns">
            <div class="column is-half">
                <a href="{{$registrationOpen ? action('Kitchen\KitchenController@create') : '#' }}"
                        {{$registrationOpen ? '' : 'disabled'}} class="is-flex is-column hover-border-red">
                    <button class="button is-light is-fullwidth has-text-red is-size-3">

                        @if($registrationOpen)
                            @lang('global.register')
                        @else
                            @lang('admin/settings.closed')
                        @endif
                    </button>

                    <div class="content">
                        <p class="title">
                            {{$registrationText}}
                        </p>
                    </div>
                </a>

                <hr>

                <a href="{{action('Auth\LoginController@showLoginForm')}}" class="is-flex is-column hover-border-red">
                    <button class="button is-light is-fullwidth has-text-red is-size-3">
                        @lang('global.login')
                    </button>
                    <div class="content">
                        <p class="title">
                            {{$loginText}}
                        </p>
                    </div>
                </a>
            </div>
            @include('logoCard')
        </div>
    </section>
@endsection