@extends('layouts.dashboard')

@section('content')
	<div class="box">
		<datatable :fields="[{
		name: 'name',
	  	sortField: 'name',
	  	filter: true
	},{
		name: 'email',
	  	sortField: 'email',
	  	filter: true
	},{
		name: 'status',
		filter: [
			'new','motherlist'
		]
	}]"
				   url="{{ action('Admin\KitchenController@list') }}"
		></datatable>
	</div>
@endsection