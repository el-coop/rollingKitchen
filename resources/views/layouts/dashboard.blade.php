@extends('layouts.plain')

@section('body')
	<div class="dashboard" v-cloak>
		<drawer>
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
		<main>
			<navbar title="Motherlist"></navbar>
			<div class="section">
				@yield('content')
			</div>
		</main>
	</div>
@endsection

