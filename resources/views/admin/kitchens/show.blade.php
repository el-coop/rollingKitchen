@extends('layouts.dashboard')

@section('title',$kitchen->user->name)

@section('content')
	<tabs>
		<tab label="@lang('kitchen/kitchen.businessInformation')">
			@include('admin.kitchens.show.kitchen')
		</tab>
		<tab label="@lang('admin/applications.applications')">
			@include('admin.kitchens.show.applications')
		</tab>
	</tabs>
@endsection

