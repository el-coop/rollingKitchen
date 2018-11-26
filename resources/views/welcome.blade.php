@extends('layouts.site')

@section('content')

    <section class="section">


        <div class="columns">
            <div class="column">

                <div class="card is-flex" style="flex-direction: column; height: 100%">
                    <div class="card-content" style="flex: 1">
                        <p class="title">
                            Klik op aanmelden als je in 2017 niet hebt meegedaan aan Het Weekend van de Rollende keukens.
                        </p>

                    </div>

                    <footer class="card-footer">
                        <button class="card-footer-item button is-dark is-size-3">

                            Aanmelden

                        </button>
                    </footer>
                </div>
            </div>
            <div class="column">


                <div class="card is-flex" style="flex-direction: column; height: 100%">
                    <div class="card-content" style="flex: 1">
                        <p class="title">
                            Klik op inloggen als je in 2017 ook al hebt meegedaan aan Het Weekend van de Rollende Keukens.
                        </p>

                    </div>

                    <footer class="card-footer">
                        <button class="card-footer-item button is-dark is-size-3">

                            Inloggen

                        </button>
                    </footer>
                </div>
            </div>
            <div class="column">

                <figure class="image is-square">

                    <img src="/images/kreeft.png" alt="kreeft">


                </figure>
            </div>
        </div>

    </section>
@endsection