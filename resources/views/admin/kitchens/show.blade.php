@extends('layouts.dashboard')

@section('title',$kitchen->user->name)

@section('content')
	<tabs>
		<tab label="Kitchen">
			@include('admin.kitchens.show.kitchen')
		</tab>
		<tab label="Applications">
			@include('admin.kitchens.show.applications')
		</tab>
	</tabs>
@endsection

