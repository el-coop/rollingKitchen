@extends('layouts.plain')

@section('body')
	<div class="dashboard">
		<drawer>
			<div class="menu" v-cloak>
				@foreach($dashboardItems as $label => $items)
					@component('components.dashboardListItem', [
						'label' => $label,
						'items' => collect($items)
					])
					@endcomponent
				@endforeach
			</div>
		</drawer>
		<div class="container is-fluid">
			@yield('content')
		</div>
	</div>
@endsection

