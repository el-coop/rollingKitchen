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