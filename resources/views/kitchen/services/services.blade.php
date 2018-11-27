<p class="title is-4">Services</p>
@foreach($services as $service)
	<div class="field">
		@if(!$service->type)
			<div class="level">
				<div class="level-left">
					<div class="control level-item">
						<input type="number" class="input" min="0"
							   @if(! $application->isOpen()) readonly @endif
							   name="services[{{$service->id}}]"
							   value="{{ $application->hasService($service) ? $application->serviceQuantity($service) : '0' }}">
					</div>
					<label class="label level-item">
						{{ $service->name }} - € {{ number_format($service->price,2) }}
					</label>
				</div>
			</div>
		@else
			<label class="checkbox">
				<input type="checkbox" value="1"
					   @if(! $application->isOpen())  onclick="return false;" @endif
					   name="services[{{$service->id}}]" {{ $application->hasService($service) ? 'checked' : '' }}>
				{{ $service->name }} - € {{ number_format($service->price,2) }}
			</label>
		@endif
	</div>
@endforeach