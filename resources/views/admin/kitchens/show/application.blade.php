<div class="tile is-ancestor">
	<div class="tile is-parent">
		<div class="tile is-child">
			<dynamic-form :init-fields="{{ $application->fullData->except('year') }}"
						  url="{{ action('Admin\ApplicationController@update', $application) }}"
						  :hide="['year']"></dynamic-form>
		</div>
	</div>
	<div class="tile is-parent">
		<div class="tile is-child box">
			<label class="label"></label>
			<select-chooser>
				<select-view label="@lang('kitchen/kitchen.businessInformation')">
					<hr>
					<dynamic-fields :fields="{{ collect([[
						'name' => 'story',
						'label' => __('kitchen/kitchen.story'),
						'value' => $application->story,
						'readonly' => true,
						'type' => 'textarea',
					]]) }}"></dynamic-fields>
				</select-view>
				<select-view label="@lang('kitchen/kitchen.kitchenInformation')">
					<hr>
					@component('admin.kitchens.show.application.dimensions', compact('application'))
					@endcomponent
				</select-view>
				<select-view label="@lang('kitchen/products.menuTab')">
					@component('kitchen.application.products', ['application' => $application, 'readonly' => true])
					@endcomponent
				</select-view>
				<select-view label="@lang('admin/services.services')">
					@include('admin.kitchens.show.application.services')
				</select-view>
				@if($application->electricDevices->count())
					<select-view label="@lang('kitchen/services.electricity')">
						@include('admin.kitchens.show.application.electricity')
					</select-view>
				@endif
				<select-view label="@lang('admin/invoices.invoices')">
					@include('admin.kitchens.show.application.invoices')
				</select-view>
			</select-chooser>
		</div>
	</div>
</div>
