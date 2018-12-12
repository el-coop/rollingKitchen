@extends('layouts.dashboard')

@section('title',$debtor->name)

@section('content')
	<div class="box tile is-ancestor">
		<div class="tile is-parent">
			<div class="tile is-child">
				<dynamic-form :init-fields="{{ $debtor->fulldata }}"
							  url="{{ action('Admin\DebtorController@update', $debtor) }}"></dynamic-form>
			</div>
		</div>
		<div class="tile is-parent is-vertical">
			<div class="tile is-child">
				@include('admin.debtors.show.invoices')
			</div>
		</div>
	</div>
@endsection

