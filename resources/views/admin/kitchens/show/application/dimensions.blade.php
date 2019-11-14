<dynamic-form :init-fields="[{
	'name': 'length',
	'label': '@lang('kitchen/dimensions.length')',
	'value': '{{ $application->length }}',
	type: 'text',
	placeholder: '@lang('kitchen/dimensions.inMeters')',
	subType: 'number',
    step: 0.1,
},{
	'name' : 'width',
	'label' : '@lang('kitchen/dimensions.width')',
	'value' : '{{ $application->width }}',
	type: 'text',
	placeholder: '@lang('kitchen/dimensions.inMeters')',
	subType: 'number',
    step: 0.1,
},{
	'name' : 'terrace_length',
	'label' : '@lang('kitchen/dimensions.terraceLength')',
	'value' : '{{ $application->terrace_length }}',
	type: 'text',
	placeholder: '@lang('kitchen/dimensions.inMeters')',
	subType: 'number',
    step: 0.1,
},{
	'name' : 'terrace_width',
	'label' : '@lang('kitchen/dimensions.terraceWidth')',
	'value' : '{{ $application->terrace_width }}',
	type: 'text',
	placeholder: '@lang('kitchen/dimensions.inMeters')',
	subType: 'number',
    step: 0.1,
}]" button-class="is-info" url="{{ action('Admin\ApplicationController@updateDimensions', $application) }}">
</dynamic-form>
