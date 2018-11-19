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
	{{ $slot }}
</datatable>
