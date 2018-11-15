@extends('layouts.guest')

@section('content')
    <div class="card">
        <header class="card-header ">
            <p class="card-header-title">@lang('misc.login')</p>
        </header>
        <div class="card-content">
            <div class="content">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">@lang('misc.email')</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control">
                                    <input class="input" type="text" placeholder="e.g. Partnership opportunity" name="email" value="{{ old('email') }}">
                                </div>
                                @if ($errors->has('email'))
                                    <p class="help is-danger">
                                        {{ $errors->first('email') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">@lang('misc.password')</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control">
                                    <input class="input" type="password" placeholder="e.g. Partnership opportunity" name="password">
                                </div>
                                @if ($errors->has('password'))
                                    <p class="help is-danger">
                                        {{ $errors->first('password') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="field is-horizontal">
                        <div class="field-label">
                            <!-- Left empty for spacing -->
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control">
                                    <button class="button is-primary">
                                        @lang('misc.login')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
