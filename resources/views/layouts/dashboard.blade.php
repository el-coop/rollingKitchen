@extends('layouts.plain')

@section('body')
	<div class="dashboard" v-cloak>
		<drawer :open="drawerOpen" @close-drawer="drawerOpen = false">
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
			<navbar title="@yield('title')" @open-drawer="drawerOpen = true">
				@component('components.logout')
				@endcomponent
				<div class="navbar-item">
					<img src="{{ asset('storage/images/logo.png') }}">
				</div>
			</navbar>
			<div class="container is-fluid scrollable">
				@yield('content')
			</div>
		</main>
	</div>
@endsection

