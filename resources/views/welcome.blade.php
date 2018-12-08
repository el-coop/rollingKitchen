@extends('layouts.site')

@section('title',__('global.welcome'))


@section('content')

    <section class="section">


        <div class="columns">
            <div class="column">

                <div class="card is-flex is-column h-100">
                    <div class="card-content fill-parent">
                        <p class="title">
                            {{$registrationText}}
                        </p>

                    </div>

                    <footer class="card-footer">
                        <a href="{{$registrationOpen ? action('Kitchen\KitchenController@create') : '#' }}" {{$registrationOpen ? '' : 'disabled'}}
                           class="card-footer-item button is-dark is-size-3">


                            @if($registrationOpen)
                                @lang('global.register')
                            @else
                                @lang('admin/settings.closed')
                            @endif

                        </a>
                    </footer>
                </div>
            </div>
            <div class="column">


                <div class="card is-flex" style="flex-direction: column; height: 100%">
                    <div class="card-content" style="flex: 1">
                        <p class="title">
                            {{$loginText}}

                        </p>

                    </div>

                    <footer class="card-footer">
                        <a href="{{action('Auth\LoginController@showLoginForm')}}"
                           class="card-footer-item button is-dark is-size-3">

                            @lang('global.login')

                        </a>
                    </footer>
                </div>
            </div>
            <div class="column">

                <figure class="image is-square">

                    <img src="/images/logo.png" alt="kreeft">


                </figure>
            </div>
        </div>

    </section>
@endsection