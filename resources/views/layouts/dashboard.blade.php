@extends('layouts.plain')

@section('body')
	<div class="dashboard" v-cloak>
		<drawer>
			<div class="menu">
				@foreach($dashboardItems as $label => $items)
					@component('components.dashboardListItem', [
						'label' => $label,
						'items' => collect($items),
						'indexLink' => $indexLink ?? false
					])
					@endcomponent
				@endforeach
			</div>
		</drawer>
		<main>
			<navbar title="@yield('title')">
				@component('components.logout')
				@endcomponent
				<div class="navbar-item">
					<img src="{{ asset('images/logo.png') }}">
				</div>
			</navbar>
			<div class="container is-fluid">
				@yield('content')
			</div>
		</main>
	</div>
@endsection

