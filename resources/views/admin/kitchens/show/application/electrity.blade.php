<p>
	<dynamic-table :columns="[{
					name: 'name',
					label: '@lang('misc.name')'
				},{
					name: 'watts',
					label: '@lang('watts')',
					subType: 'number',
					type: 'text',
				}]" :init-fields="{{ $application->electricDevices }}"
				   action="/kitchen/applications/{{$application->id}}/devices"></dynamic-table>
</p>
<hr>
@include('kitchen.services.sockets')