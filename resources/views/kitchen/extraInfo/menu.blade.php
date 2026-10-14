<p class="mb-5">{{ app('settings')->get('application_extra_info_menu_text_' . App::getLocale()) }}</p>
@component('kitchen.extraInfo.yesNo', [
    'application' => $application,
    'name' => 'religious_dishes',
    'label' => __('kitchen/extraInfo.religiousDishes'),
    'readonly' => $readonly ?? false,
])
    <p>@lang('kitchen/extraInfo.religiousNotes')</p>
    <div class="table-container mb-5">
        <table class="table is-fullwidth">
            <thead>
            <tr>
                <th>@lang('kitchen/extraInfo.dish')</th>
                <th>@lang('kitchen/extraInfo.religiousNote')</th>
            </tr>
            </thead>
            <tbody>
            @foreach($application->products->where('category', '!=', 'other') as $product)
                <tr>
                    <td v-pre>{{ $product->name }}</td>
                    <td>
                        <input class="input" type="text" name="religious_notes[{{ $product->id }}]"
                               value="{{ ($readonly ?? false) ? $product->religious_note : old("religious_notes.{$product->id}", $product->religious_note) }}"
                               @if(($readonly ?? false) || !$application->isExtraInfoOpen()) disabled @endif>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endcomponent
@component('kitchen.extraInfo.yesNo', [
    'application' => $application,
    'name' => 'vegan_dishes',
    'label' => __('kitchen/extraInfo.veganDishes'),
    'readonly' => $readonly ?? false,
])
    <p class="mb-2">@lang('kitchen/extraInfo.veganProducts')</p>
    <div class="mb-5">
        @foreach($application->products->where('category', '!=', 'other') as $product)
            <div class="field">
                <label class="checkbox">
                    <input type="checkbox" name="vegan_products[]" value="{{ $product->id }}"
                           @if(collect(($readonly ?? false) ? [] : old('vegan_products'))->contains($product->id) || (!old('vegan_products') && $product->vegan)) checked @endif
                           @if(($readonly ?? false) || !$application->isExtraInfoOpen()) disabled @endif>
                    <span v-pre>{{ $product->name }}</span>
                </label>
            </div>
        @endforeach
    </div>
@endcomponent
