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
			<navbar title="@yield('title')"></navbar>
			<div class="container is-fluid" style="margin-top: 1rem">
				@yield('content')
			</div>
		</main>
	</div>
@endsection

