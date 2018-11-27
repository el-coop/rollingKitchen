<p class="title is-4">Sockets</p>
@foreach(['none','Electric 2 x 230V, 16A Blue (1 Fase, CEE) € 125','Electric 3 x 230V, 16A Blue (1 Fase, CEE) € 250','Electric 1 x 400V 16A Red (3 Fase, CEE, 5 pole) € 350','Electric 1 x 400V 32A Red (3 Fase, CEE, 5 pole) € 350','Electric 2 x 400V 32A Rood (3 Fase, CEE, 5 pole) € 650
'] as $key => $value)
	<div class="field">
		<label class="radio">
			<input type="radio"
				   @if(!$application->isOpen() && $application->socket != $key ) disabled @endif
				   name="socket" value="{{$key}}" {{ $application->socket == $key ? 'checked' : '' }}>
			{{ $value }}
		</label>
	</div>
@endforeach