@extends('layouts.site')

@section('title',__('global.welcome'))


@section('content')

    <section class="section">

        <div class="columns">
            <div class="column">
                <a href="{{$registrationOpen ? action('Kitchen\KitchenController@create') : '#' }}"
                   class="is-flex is-column h-100 hover-border">
                    <div class="content fill-parent">
                        <p class="title">
                            {{$registrationText}}
                        </p>
                    </div>
                    <button class="button is-light has-text-red is-size-3" {{$registrationOpen ? '' : 'disabled'}}>
                        @if($registrationOpen)
                            @lang('global.register')
                        @else
                            @lang('admin/settings.closed')
                        @endif
                    </button>
                </a>
            </div>

            <div class="column">
                <a href="{{action('Auth\LoginController@showLoginForm')}}"
                   class="is-flex is-column h-100 hover-border">
                    <div class="content fill-parent">
                        <p class="title">
                            {{$loginText}}
                        </p>
                    </div>
                    <button class="button is-light has-text-red is-size-3">
                        @lang('global.login')
                    </button>
                </a>
            </div>

            <div class="column is-half is-hidden-touch">
                <figure class="image is-5by3">
                    <img src="/images/logo20192.png" alt="kreeft">
                </figure>
            </div>
        </div>

    </section>
@endsection