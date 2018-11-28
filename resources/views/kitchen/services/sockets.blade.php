<p class="title is-4">@lang('kitchen/services.sockets')</p>
@foreach([__('kitchen/services.none'),__('kitchen/services.2X230'),__('kitchen/services.3x230'),__('kitchen/services.1x400-16'),__('kitchen/services.1x400-32'),__('kitchen/services.2x400')] as $key => $value)
	<div class="field">
		<label class="radio">
			<input type="radio"
				   @if(!$application->isOpen() && $application->socket != $key ) disabled @endif
				   name="socket" value="{{$key}}" {{ $application->socket == $key ? 'checked' : '' }}>
			{{ $value }}
		</label>
	</div>
@endforeach