@extends('layouts.plain')

@section('body')
	<div class="dashboard">
		<drawer>
			<div class="menu" v-cloak>
				<list-section label="General">
					<li><a>Dashboard</a></li>
					<li><a>Customers</a></li>
				</list-section>

				<list-section label="Administration">
					<li><a>Team Settings</a></li>
					<li><a>Invitations</a></li>
					<li><a>Cloud Storage Environment Settings</a></li>
					<li><a>Authentication</a></li>
				</list-section>

				<list-section label="Transactions">
					<li><a>Payments</a></li>
					<li><a>Transfers</a></li>
					<li><a>Balance</a></li>
				</list-section>
			</div>
		</drawer>
		<div class="container is-fluid">
			@yield('content')
		</div>
	</div>
@endsection

