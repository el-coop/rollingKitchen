<dynamic-fields :fields="[{
	name: 'length',
	label: '@lang('kitchen/dimensions.length')',
	value: '{{ old('length', $application->length != 0 ? $application->length  : '') }}',
	readonly: {{ !$application->isOpen() ? 'true' : 'false'}},
	type: 'text',
	subType: 'number',
	step: 0.1,
	placeholder: '@lang('kitchen/dimensions.inMeters')',
	error: {{ $errors->has('length') ? collect($errors->get('length')) : 'null'}},
},{
	name : 'width',
	label : '@lang('kitchen/dimensions.width')',
	value: '{{ old('width', $application->width != 0 ? $application->width  : '') }}',
	readonly: {{ ! $application->isOpen() ? 'true' : 'false'}},
	type: 'text',
	subType: 'number',
	step: 0.1,
	placeholder: '@lang('kitchen/dimensions.inMeters')',
	error: {{ $errors->has('width') ? collect($errors->get('width')) : 'null'}},
}]">
</dynamic-fields>
<dynamic-fields :fields="{{ collect([[
	'name' => 'description',
	'label' => __('kitchen/dimensions.description'),
	'value' => old('description', $application->description),
	'readonly' => !$application->isOpen(),
	'type' => 'textarea',
	'placeholder' => __('kitchen/dimensions.descriptionPlaceholder'),
	'error' => $errors->has('description') ? $errors->get('description') : null,
]]) }}">
</dynamic-fields>
<div class="field mt-2">
    <label class="label">@lang('kitchen/dimensions.sketch')</label>
    @if(!$application->sketches()->exists())
        <div class="m-2 is-block">
            <label>@lang('kitchen/dimensions.sketchExample')</label>
            <figure class="image">
                <img class="sketch-image"  src="{{ asset('/images/sketch.jpg')}}">
            </figure>
        </div>
    @endif
    <image-manager url="{{ action('Kitchen\KitchenController@storeApplicationSketch', $application) }}" :data="{
			_token: '{{csrf_token()}}'
		}" :init-images="{{ $application->sketches }}" delete-url="/kitchen/applications/{{ $application->id }}/photo">
    </image-manager>
</div>
