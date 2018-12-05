<!doctype html>
<html lang="{{ App::getLocale() }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>@yield('title') | {{ config('app.name') }}</title>

	<!-- Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
	<link href="{{ mix('/css/app.css') }}" rel="stylesheet">


</head>
<body>
<div id="app">
	@yield('body')
	@if(session()->has('toast'))
		<toast message="{{ session()->get('toast')['message'] }}" title="{{ session()->get('toast')['title'] }}"
			   type="{{ session()->get('toast')['type'] }}"></toast>
	@endif
</div>
<script>
	var translations = @json(__('vue'))
</script>
<script src="{{ mix('/js/app.js') }}"></script>
@yield('scripts')
</body>
</html>
