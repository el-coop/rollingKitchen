<div class="columns">
	<div class="column">
		<p class="title is-4">Electric Devices</p>
		<p class="subtitle">Please inform us of ALL electrical equipment you intend to plug in during the festival. </p>
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
						   @if($application->isOpen())action="/kitchen/applications/{{$application->id}}/devices" @endif></dynamic-table>
		</p>
		<hr>
		@include('kitchen.services.sockets')
	</div>
	<div class="column">
		@include('kitchen.services.services')
	</div>
</div>
