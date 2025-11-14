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
},{
	name : 'terrace_length',
	label : '@lang('kitchen/dimensions.terraceLength')',
	value: '{{ old('terrace_length', $application->terrace_length) }}',
	readonly: {{ ! $application->isOpen() ? 'true' : 'false'}},
	type: 'text',
	subType: 'number',
	step: 0.1,
	placeholder: '@lang('kitchen/dimensions.inMeters')',
    error: {{ $errors->has('terrace_length') ? collect($errors->get('terrace_length')) :  'null'}},
},{
	name : 'terrace_width',
	label : '@lang('kitchen/dimensions.terraceWidth')',
	value: '{{ old('terrace_width', $application->terrace_width) }}',
	readonly: {{ ! $application->isOpen() ? 'true' : 'false'}},
	type: 'text',
	subType: 'number',
	step: 0.1,
	placeholder: '@lang('kitchen/dimensions.inMeters')',
    error: {{ $errors->has('terrace_width') ? collect($errors->get('terrace_width')) : 'null'}},
}]">
</dynamic-fields>
<div class="field mt-2">
    <label class="label">@lang('kitchen/dimensions.sketch')</label>
    @if(!$application->sketches()->exists())
        <div class="m-2">
            <label>@lang('kitchen/dimensions.sketchExample')</label>
            <figure class="image is-128x128">
                <img src="{{ asset('/images/sketch.HEIC')}}">
            </figure>
        </div>
    @endif
    <image-manager url="{{ action('Kitchen\KitchenController@storeApplicationSketch', $application) }}" :data="{
			_token: '{{csrf_token()}}'
		}" :init-images="{{ $application->sketches }}" delete-url="/kitchen/applications/{{ $application->id }}/photo">
    </image-manager>
</div>
