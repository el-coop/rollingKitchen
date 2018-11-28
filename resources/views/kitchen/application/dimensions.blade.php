<dynamic-fields :fields="[{
	name: 'length',
	label: '@lang('kitchen/dimensions.length')',
	value: '{{ $application->length }}',
	readonly: {{ !$application->isOpen() ? 'true' : 'false'}},
	type: 'text',
	subType: 'number'
},{
	name : 'width',
	label : '@lang('kitchen/dimensions.width')',
	value : '{{ $application->width }}',
	readonly: {{ ! $application->isOpen() ? 'true' : 'false'}},
	type: 'text',
	subType: 'number'
},{
	name : 'terrace_length',
	label : '@lang('kitchen/dimensions.terraceLength')',
	value : '{{ $application->terrace_length }}',
	readonly: {{ ! $application->isOpen() ? 'true' : 'false'}},
	type: 'text',
	subType: 'number'
},{
	name : 'terrace_width',
	label : '@lang('kitchen/dimensions.terraceWidth')',
	value : '{{ $application->terrace_width }}',
	readonly: {{ ! $application->isOpen() ? 'true' : 'false'}},
	type: 'text',
	subType: 'number'
},{
	name : 'seats',
	label : '@lang('kitchen/dimensions.terraceSeats')',
	value : '{{ $application->seats }}',
	readonly: {{ ! $application->isOpen() ? 'true' : 'false'}},
	type: 'text',
	subType: 'number'
}]">
</dynamic-fields>