<p class="title is-4">@lang('kitchen/kitchen.services')</p>
@foreach($countableServices as $service)
	<div class="field has-addons are-labels">
		<div class="control">
			<input type="number" class="input is-short-numeric" min="0"
				   @if(! $application->isOpen()) readonly @endif
				   name="services[{{$service->id}}]"
				   value="{{ $application->hasService($service) ? $application->serviceQuantity($service) : '0' }}">
		</div>
		<div class="control">
			<a class="button is-static">
				<b>{{ $service->{ 'name_' . App::getLocale()} }}
					€ {{ number_format($service->price,2,$decimalPoint,$thousandSeparator) }}</b>
			</a>
		</div>
	</div>
@endforeach
@foreach($checkableServices as $service)
	<div class="field has-addons are-labels">
		<div class="control">
			<label class="button">
				<input type="checkbox" value="1" id="services_{{$service->id}}"
					   @if(! $application->isOpen())  onclick="return false;" @endif
					   name="services[{{$service->id}}]" {{ $application->hasService($service) ? 'checked' : '' }}>
			</label>
		</div>
		<div class="control">
			<button class="button is-static">
				<b>{{ $service->{ 'name_' . App::getLocale()} }}
					€ {{ number_format($service->price,2,$decimalPoint,$thousandSeparator) }}</b>
			</button>
		</div>
	</div>
@endforeach

<div class="field has-addons are-labels">
	<div class="control">
		<label class="button">
			<input type="checkbox" value="1" checked onclick="return false;">
		</label>
	</div>
	<div class="control">
		<button class="button is-static">
			<b>@lang('kitchen/services.trash')
				€ {{ number_format(50,2,$decimalPoint,$thousandSeparator) }}</b>
		</button>
	</div>
</div>