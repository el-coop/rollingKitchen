<p class="title is-4">@lang('kitchen/services.sockets')</p>
<div class="field">
	<label class="radio">
		<input type="radio"
			   @if(!$application->isOpen()) disabled @endif
			   value="0"
			   name="socket" {{ $application->services()->where('category','socket')->count() ? '' : 'checked' }}>
		@lang('kitchen/services.none')
	</label>
</div>
@foreach($sockets as $socket)
	<div class="field">
		<label class="radio">
			<input type="radio"
				   @if(!$application->isOpen()) disabled @endif
				   name="socket"
				   value="{{ $socket->id }}" {{ $application->hasService($socket) ? 'checked' : '' }}>
			{{ $socket->{ 'name_' . App::getLocale()} }}
			€ {{ number_format($socket->price,2,$decimalPoint,$thousandSeparator) }}
		</label>
	</div>
@endforeach