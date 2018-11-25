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
				<div class="navbar-item">
					<form action="{{ action('Auth\LoginController@logout') }}" method="post">
						@csrf
						<button type="submit" class="button is-danger is-inverted">
							<font-awesome-icon icon="sign-out-alt" class="icon" fixed-width></font-awesome-icon>
						</button>
					</form>
				</div>
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

