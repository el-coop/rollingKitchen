<?php
$table = str_replace('/', '.', Request::path()) . 'Table';
if (!isset($fields)) {
	$fields = collect(config("{$table}.fields"));
	$fields = $fields->map(function ($field) {
		if (isset($field['title'])) {
			$field['title'] = __($field['title']);
		}
		if (is_array($field['filter'] ?? false)) {
			foreach ($field['filter'] as $key => $value) {
				$field['filter'][$key] = __($value);
			}
		}
		return $field;
	});
}
?>
<datatable :field-settings="{{ $fields }}"
		   :extra-params="{
		   		table: '{{$table}}'
		   }"
		   :translations="{
		   		yes: '@lang('datatable.yes')',
		   		no: '@lang('datatable.no')',
				motherlist: '@lang('datatable.motherlist')',
		   		new: '@lang('datatable.new')',

		   }"
		   url="\admin\datatable"
		   :labels="{
		   		pagination: '@lang('datatable.pagination')',
		   		noPagination: '@lang('datatable.noPagination')',
		   		next: '@lang('datatable.next')',
		   		prev: '@lang('datatable.prev')',
		   		filters: '@lang('datatable.filters')',
		   		filter: '@lang('datatable.filter')',
		   		clear: '@lang('datatable.clear')',
		   }"
>
	@isset($buttons)
	<template slot="buttons">{{$buttons}}</template>
	@endisset
	@if(trim($slot) !== '')
		{{ $slot }}
	@else
		<template slot-scope="{object, onUpdate}" v-if="object">
			<div class="title is-size-3 has-text-centered" v-text="object.name"></div>
			<dynamic-form :url="`{{Request::url() }}/${object.id}`" :on-data-update="onUpdate"></dynamic-form>
		</template>
	@endif
</datatable>
