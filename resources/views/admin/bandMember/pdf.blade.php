<!doctype html>
<html lang="{{ App::getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <link href="{{ env('APP_URL') . mix('/css/app.css') }}" rel="stylesheet">

    <style>
        * {
            font-size: 12px;
        }
    </style>
</head>
<body>
<div class="section">
    <div class="container">
        <div class="is-pulled-left">
            <figure class="image is-32x32" style="margin-left: 35px">
                <img src="{{ asset('/storage/images/logo.png')}}">
            </figure>
            <div class="has-text-4">{{ config('app.name') }}</div>
        </div>
        <div class="is-pulled-right">
            <b>@lang('admin/invoices.date')
                : </b> {{ ucfirst(\Carbon\Carbon::now()->isoFormat('dddd DD MMMM Y')) }}
        </div>
    </div>
</div>
<div class="is-clearfix"></div>
<div class="section" style="padding-bottom: 10px; margin-bottom: 10px">
    @foreach($data as $key => $value)
        <div>
            <span class="has-text-weight-bold">{{ $key }}:</span> {{ $value }}
        </div>
    @endforeach
</div>
<div class="is-clearfix"></div>
<div class="section">
    <div class="container">
        @foreach($images as $image)
            <img src="{{ $image }}">
        @endforeach
    </div>
</div>
</body>
</html>
