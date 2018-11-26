<dynamic-fields :fields="[{
	name: 'length',
	label: '@lang('admin/kitchens.length')',
	value: '{{ $application->length }}',
	readonly: {{ !$application->isOpen() ? 'true' : 'false'}},
	type: 'text',
	subType: 'number'
},{
	name : 'width',
	label : '@lang('admin/kitchens.width')',
	value : '{{ $application->width }}',
	readonly: {{ ! $application->isOpen() ? 'true' : 'false'}},
	type: 'text',
	subType: 'number'
},{
	name : 'terrace_length',
	label : '@lang('admin/kitchens.terraceLength')',
	value : '{{ $application->terrace_length }}',
	readonly: {{ ! $application->isOpen() ? 'true' : 'false'}},
	type: 'text',
	subType: 'number'
},{
	name : 'terrace_width',
	label : '@lang('admin/kitchens.terraceWidth')',
	value : '{{ $application->terrace_width }}',
	readonly: {{ ! $application->isOpen() ? 'true' : 'false'}},
	type: 'text',
	subType: 'number'
},{
	name : 'seats',
	label : '@lang('admin/kitchens.terraceSeats')',
	value : '{{ $application->seats }}',
	readonly: {{ ! $application->isOpen() ? 'true' : 'false'}},
	type: 'text',
	subType: 'number'
}]">
</dynamic-fields>