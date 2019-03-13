<schedule :budget="{{ $budget }}" :init-budget="{{ $initBudget }}">
	<template #default="{submitting, updateBudget}">
		<calendar start-date="{{ \Carbon\Carbon::now() }}" :start-hour="13" options-title="@lang('admin/artists.bands')"
				  :init-data="{{ $schedules }}"
				  :options="{{ $bands }}">
			<template #entry="{rawData,processedData, edit, init, dateTime}">
				<calendar-schedule-display v-if="processedData" :data="processedData" :edit="edit" :init="init"
										   :bands="{{$bands}}" :stages="{{ $stages }}"
										   :date-time="dateTime" :on-update="updateBudget"></calendar-schedule-display>
			</template>
			<template #options>
				<div class="buttons">
					<button class="button is-primary is-fullwidth" type="submit"
							:class="{'is-loading' : submitting}">@lang('global.save')</button>
				</div>
			</template>
			<template #modal="{input, output}">
				<calendar-modal :input="input" :output="output" :stages="{{ $stages }}"
								:bands="{{$bands}}"></calendar-modal>
			</template>
		</calendar>
		<div class="buttons mt-1 is-invisible-tablet">
			<button class="button is-primary is-fullwidth" type="submit"
					:class="{'is-loading' : submitting}">@lang('global.save')</button>
		</div>
	</template>
</schedule>
