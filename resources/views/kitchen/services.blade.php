<div class="columns">
	<div class="column">
		<p class="title is-4">@lang('kitchen/services.electrictyTitle')</p>
		<p class="subtitle">@lang('kitchen/services.electrictySubtitle')</p>
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

						   @if($application->isOpen())action="/kitchen/applications/{{$application->id}}/devices" @endif></dynamic-table>
		</p>
		<hr>
		@include('kitchen.services.sockets')
	</div>
	<div class="column">
		@include('kitchen.services.services')
	</div>
</div>
<div class="buttons has-content-justified-center">
	@if($application->isOpen())
		<confirmation-submit label="@lang('kitchen/kitchen.submitReview')"
							 title="@lang('kitchen/kitchen.submitConfirmTitle')"
							 subtitle="@lang('kitchen/kitchen.submitConfirmSubtitle')" yes-text="@lang('global.yes')"
							 no-text="@lang('global.no')" name="review" value="1"
							 id="reviewButton"></confirmation-submit>
	@endif
</div>
