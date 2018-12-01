<dynamic-form :init-fields="[{
	'name': 'length',
	'label': '@lang('kitchen/dimensions.length')',
	'value': '{{ $application->length }}',
	type: 'text',
	subType: 'number'
},{
	'name' : 'width',
	'label' : '@lang('kitchen/dimensions.width')',
	'value' : '{{ $application->width }}',
	type: 'text',
	subType: 'number'
},{
	'name' : 'terrace_length',
	'label' : '@lang('kitchen/dimensions.terraceLength')',
	'value' : '{{ $application->terrace_length }}',
	type: 'text',
	subType: 'number'
},{
	'name' : 'terrace_width',
	'label' : '@lang('kitchen/dimensions.terraceWidth')',
	'value' : '{{ $application->terrace_width }}',
	type: 'text',
	subType: 'number'
},{
	'name' : 'seats',
	'label' : '@lang('kitchen/dimensions.terraceSeats')',
	'value' : '{{ $application->seats }}',
	type: 'text',
	subType: 'number'
}]" button-class="is-info" url="{{ action('Admin\ApplicationController@updateDimensions', $application) }}">
</dynamic-form>