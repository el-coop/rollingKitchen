@extends('layouts.plain')

@section('body')
	<div class="dashboard">
		<drawer v-cloak>
			<div class="menu">
				@foreach($dashboardItems as $label => $items)
					@component('components.dashboardListItem', [
						'label' => $label,
						'items' => collect($items)
					])
					@endcomponent
				@endforeach
			</div>
		</drawer>
		<div>
			<navbar title="Motherlist"></navbar>
			<div class="container is-fluid">
				@yield('content')
			</div>
		</div>
	</div>
@endsection

