@extends('layouts.plain')

@section('body')
        <main>
            <navbar title="@lang('global.title')"></navbar>
            <div class="section">
                @yield('content')
            </div>
        </main>
@endsection
