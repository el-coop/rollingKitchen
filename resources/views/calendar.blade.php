@extends('layouts.site')

@section('content')
	<calendar start-date="{{ \Carbon\Carbon::now() }}" :start-hour="17"></calendar>
@endsection
