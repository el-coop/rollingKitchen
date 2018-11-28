@extends('layouts.site')

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
                        <a href="{{action('Kitchen\KitchenController@create')}}" class="card-footer-item button is-dark is-size-3">

                            Aanmelden

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
                        <a href="{{action('Auth\LoginController@showLoginForm')}}" class="card-footer-item button is-dark is-size-3">

                            Inloggen

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