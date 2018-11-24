<p class="title is-4">Services</p>
@foreach($services as $service)
	<div class="field">
		<label class="checkbox">
			<input type="checkbox"
				   name="services[{{$service->id}}]" {{ $application->hasService($service) ? 'checked' : '' }}>
			{{ $service->name }} - <b>€ {{ number_format($service->price,2) }}</b>
		</label>
	</div>
@endforeach