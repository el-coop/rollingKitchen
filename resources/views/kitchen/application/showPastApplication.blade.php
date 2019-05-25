<div class="tile is-ancestor">

	<div class="tile is-parent">
		<div class="tile is-child">
			<dynamic-fields :hide="['year']" :fields="{{ $application->fullData->map(function ($item) {
$item['readonly'] = true;
return $item;
}) }}"
			></dynamic-fields>
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
					@component('kitchen.application.dimensions', compact('application'))
					@endcomponent
				</select-view>
				<select-view label="@lang('kitchen/services.electricity')">
					@include('kitchen.application.electricity')
				</select-view>
				<select-view label="@lang('admin/services.services')">
					@include('kitchen.application.pastServices')
				</select-view>
			</select-chooser>
		</div>
	</div>
</div>
