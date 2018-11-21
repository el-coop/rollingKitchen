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
				<select-view label="Products">
					@component('admin.kitchens.show.application.products', compact('application'))
					@endcomponent
				</select-view>
				<select-view label="Dimensions">
					@component('admin.kitchens.show.application.dimensions', compact('application'))
					@endcomponent
				</select-view>
				<select-view label="Services"></select-view>
			</select-chooser>
		</div>
	</div>
</div>