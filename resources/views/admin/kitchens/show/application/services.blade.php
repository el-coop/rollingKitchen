<services-form url="{{ action('Admin\ApplicationController@updateServices', $application) }}">
    @foreach($application->services as $service)
        <div class="field has-addons are-labels">
            @if(!$service->type)
                <div class="control">
                    <input type="number" class="input is-short-numeric" min="0"
                           name="services[{{$service->id}}]"
                           value="{{ $service->pivot->quantity > 0 ? $service->pivot->quantity : '0' }}">
                </div>
                <div class="control">
                    <a class="button is-static">
                        <b>{{ $service->{ 'name_' . App::getLocale()} }}
                            € {{ number_format($service->price,2,$decimalPoint,$thousandSeparator) }}</b>
                    </a>
                </div>
            @else

                <div class="control">
                    <label class="button">
                        <input type="checkbox" value="1" id="services_{{$service->id}}"
                               name="services[{{$service->id}}]" {{ $service->pivot->quantity > 0 ? 'checked' : '' }}>
                    </label>
                </div>
                <div class="control">
                    <button class="button is-static">
                        <b>{{ $service->{ 'name_' . App::getLocale()} }}
                            € {{ number_format($service->price,2,$decimalPoint,$thousandSeparator) }}</b>
                    </button>
                </div>
            @endif
        </div>
    @endforeach

    @foreach($services->whereNotIn('id',$application->services->pluck('id')) as $service)
        <div class="field has-addons are-labels">
            @if(!$service->type)
                <div class="control">
                    <input type="number" class="input is-short-numeric" min="0"
                           name="services[{{$service->id}}]"
                           value="0">
                </div>
                <div class="control">
                    <a class="button is-static">
                        <b>{{ $service->{ 'name_' . App::getLocale()} }}
                            € {{ number_format($service->price,2,$decimalPoint,$thousandSeparator) }}</b>
                    </a>
                </div>
            @else

                <div class="control">
                    <label class="button">
                        <input type="checkbox" value="1" id="services_{{$service->id}}"
                               name="services[{{$service->id}}]">
                    </label>
                </div>
                <div class="control">
                    <button class="button is-static">
                        <b>{{ $service->{ 'name_' . App::getLocale()} }}
                            € {{ number_format($service->price,2,$decimalPoint,$thousandSeparator) }}</b>
                    </button>
                </div>
            @endif
        </div>
    @endforeach
</services-form>
