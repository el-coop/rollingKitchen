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
				<select-view label="@lang('admin/applications.products')">
					@component('kitchen.application.products', compact('application'))
					@endcomponent
				</select-view>
				<select-view label="@lang('kitchen/dimensions.dimensions')">
					<hr>
					@component('admin.kitchens.show.application.dimensions', compact('application'))
					@endcomponent
				</select-view>
				<select-view label="@lang('kitchen/services.electricity')">
					@include('admin.kitchens.show.application.electricity')
				</select-view>
				<select-view label="@lang('admin/services.services')">
					@include('admin.kitchens.show.application.services')
				</select-view>
				<select-view label="@lang('admin/invoices.invoices')">
					@include('admin.kitchens.show.application.invoices')
				</select-view>
			</select-chooser>
		</div>
	</div>
</div>
