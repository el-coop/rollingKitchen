<ajax-form>
	<div class="buttons">
		<button class="button is-dark">SUBMIT</button>
	</div>
	<calendar start-date="{{ \Carbon\Carbon::now() }}" :start-hour="13" options-title="@lang('admin/artists.bands')"
			  :init-data="{}"
			  :options="{{ $bands }}">
		<template #entry="{rawData,processedData, edit, init, dateTime}">
			<calendar-schedule-display v-if="processedData" :data="processedData" :edit="edit" :init="init"
									   :bands="{{$bands}}" :stages="{{ $stages }}"
									   :date-time="dateTime"></calendar-schedule-display>
		</template>
		<template #modal="{input, output}">
			<calendar-modal :input="input" :output="output" :stages="{{ $stages }}"
							:bands="{{$bands}}"></calendar-modal>
		</template>
	</calendar>
</ajax-form>
