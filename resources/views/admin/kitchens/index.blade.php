@extends('layouts.dashboard')

@section('content')
	<datatable :fields="[{
		name: 'name',
	  	sortField: 'name'
	},{
		name: 'place'
	}
	]"></datatable>
@endsection