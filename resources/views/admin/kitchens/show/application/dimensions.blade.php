<dynamic-form :init-fields="[{
	'name': 'length',
	'label': '@lang('admin/kitchens.length')',
	'value': '{{ $application->length }}',

	type: 'text',
	subType: 'number'
},{
	'name' : 'width',
	'label' : '@lang('admin/kitchens.width')',
	'value' : '{{ $application->width }}',
	type: 'text',
	subType: 'number'
},{
	'name' : 'terrace_length',
	'label' : '@lang('admin/kitchens.terraceLength')',
	'value' : '{{ $application->terrace_length }}',
	type: 'text',
	subType: 'number'
},{
	'name' : 'terrace_width',
	'label' : '@lang('admin/kitchens.terraceWidth')',
	'value' : '{{ $application->terrace_width }}',
	type: 'text',
	subType: 'number'
},{
	'name' : 'seats',
	'label' : '@lang('admin/kitchens.terraceSeats')',
	'value' : '{{ $application->seats }}',
	type: 'text',
	subType: 'number'
}]" button-class="is-info" url="{{ action('Admin\ApplicationController@updateDimensions', $application) }}">
</dynamic-form>