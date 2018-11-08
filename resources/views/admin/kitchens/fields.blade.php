@extends('layouts.dashboard')

@section('content')
    <div class="box">
        <datatable :fields="[{
		name: 'name',
	  	sortField: 'Name',
	  	filter: true
	},{
		name: 'type',
	  	sortField: 'Type',
	  	filter: true
	}"
                   url="{{ action('Admin\KitchenController@getFields') }}"
        ></datatable>
    </div>
@endsection