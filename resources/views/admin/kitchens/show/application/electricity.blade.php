<p>
	<dynamic-table :columns="[{
					name: 'name',
					label: '@lang('global.name')'
				},{
					name: 'watts',
					label: '@lang('kitchen/services.watts')',
					subType: 'number',
					type: 'text',
					callback: 'localNumber'
				}]" :init-fields="{{ $application->electricDevices }}"
				   action="/kitchen/applications/{{$application->id}}/devices"></dynamic-table>
</p>
<hr>
<p class="title is-4">@lang('kitchen/services.sockets')</p>
@foreach([__('kitchen/services.none'),__('kitchen/services.2X230'),__('kitchen/services.3x230'),__('kitchen/services.1x400-16'),__('kitchen/services.1x400-32'),__('kitchen/services.2x400')] as $key => $value)
	@if($application->socket == $key)
		{{ $value }}
	@endif
@endforeach