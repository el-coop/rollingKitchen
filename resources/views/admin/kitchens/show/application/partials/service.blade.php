<div class="field has-addons are-labels">
    @if($service->conditions !== null)
        <div class="control">
            <label class="button">
                <input type="radio"
                       id="services_{{$service->id}}"
                       value="{{$service->price}}"
                       name="services[{{$service->id}}]"
                    {{ $checkedBasePrice ? 'checked' : '' }}>
            </label>
        </div>
        <div class="control">
            <button class="button is-static">
                <b>{{ $service->{ 'name_' . App::getLocale()} }}
                    € {{ number_format($service->price,2,$decimalPoint,$thousandSeparator) }}</b>
            </button>
        </div>
        @foreach($service->conditions as $condition)
            <div class="control">
                <label class="button">
                    <input type="radio"
                           value="{{ $condition['price'] }}"
                           id="services_{{$service->id}}"
                           name="services[{{$service->id}}]"
                        {{ $checkedCondition($condition['price']) ? 'checked' : '' }}>
                </label>
            </div>
            <div class="control">
                <button class="button is-static">
                    <b>{{ $condition['name_' . App::getLocale()] }}
                        € {{ number_format($condition['price'],2,$decimalPoint,$thousandSeparator) }}</b>
                </button>
            </div>
        @endforeach
    @elseif(!$service->type)
        <div class="control">
            <input type="number"
                   class="input is-short-numeric"
                   min="0"
                   name="services[{{$service->id}}]"
                   value="{{ $quantity }}">
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
                <input type="checkbox"
                       value="1"
                       id="services_{{$service->id}}"
                       name="services[{{$service->id}}]"
                    {{ $quantity > 0 ? 'checked' : '' }}>
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
