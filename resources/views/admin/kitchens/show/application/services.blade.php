@foreach($application->services as $service)
	<div class="field">
		@if(!$service->type)
			<div class="level">
				<div class="level-left">
					<div class="control level-item">
						<input type="number" class="input" min="0"
							   name="services[{{$service->id}}]"
							   value="{{ $service->pivot->quantity }}">
					</div>
					<label class="label level-item">
						{{ $service->name }} - € {{ number_format($service->price,2) }}
					</label>
				</div>
			</div>
		@else
			<label class="checkbox">
				<input type="checkbox" value="1"
					   name="services[{{$service->id}}]" checked>
				{{ $service->name }} - € {{ number_format($service->price,2) }}
			</label>
		@endif
	</div>
@endforeach