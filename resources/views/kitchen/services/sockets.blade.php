<p class="title is-4">@lang('kitchen/services.sockets')</p>
<div class="field has-addons are-labels">
	<div class="control">
		<label class="button">
			<input type="radio"
				   @if(! $application->isOpen())  onclick="return false;" @endif
				   value="0"
				   name="socket" {{ $application->services->contains	('category','socket') ? '' : 'checked' }}>
		</label>
	</div>
	<div class="control">
		<button class="button is-static">
			<b>@lang('kitchen/services.none')</b>
		</button>
	</div>
</div>
@foreach($sockets as $socket)
	<div class="field has-addons are-labels">
		<div class="control">
			<label class="button">
				<input type="radio"
					   @if(! $application->isOpen())  onclick="return false;" @endif
					   name="socket"
					   value="{{ $socket->id }}" {{ $application->hasService($socket) ? 'checked' : '' }}>
			</label>
		</div>
		<div class="control">
			<button class="button is-static">
				<b>{{ $socket->{ 'name_' . App::getLocale()} }}
					€ {{ number_format($socket->price,2,$decimalPoint,$thousandSeparator) }}</b>
			</button>
		</div>
	</div>
@endforeach
