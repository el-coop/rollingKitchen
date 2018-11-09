<?php
$table = str_replace('/', '.', Request::path()) . 'Table';
if (!isset($fields)) {
	$fields = collect(config("{$table}.fields"));
	$fields = $fields->map(function ($field) {
		if (isset($field['title'])) {
			$field['title'] = __($field['title']);
		}
		return $field;
	});

}
?>
<datatable :fields="{{ $fields }}"
		   :extra-params="{
		   		table: '{{$table}}'
		   }"
		   url="{{ action('DatatableController@list') }}"
		   :labels="{
		   		pagination: '@lang('datatable.pagination')',
		   		noPagination: '@lang('datatable.noPagination')',
		   		next: '@lang('datatable.next')',
		   		prev: '@lang('datatable.prev')',
		   		filters: '@lang('datatable.filters')',
		   		filter: '@lang('datatable.filter')',
		   		clear: '@lang('datatable.clear')',
		   }"
></datatable>