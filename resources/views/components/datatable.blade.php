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
           @isset($editWidth)
           :edit-width="{{$editWidth}}"
           @endisset
           url="\datatable"
           :labels="{
		   		pagination: '@lang('datatable.pagination')',
		   		noPagination: '@lang('datatable.noPagination')',
		   		next: '@lang('datatable.next')',
		   		prev: '@lang('datatable.prev')',
		   		filters: '@lang('datatable.filters')',
		   		filter: '@lang('datatable.filter')',
		   		clear: '@lang('datatable.clear')',
		   }"
           :init-filters="{{ $filters ?? '{}' }}"
            @isset($deleteButton)
                :delete="true"
            @endif
>
    @isset($buttons)
        <template slot="buttons" slot-scope="{actions}">{{$buttons}}</template>
    @endisset
    @if(trim($slot) !== '')
        {{ $slot }}
    @else
        <template slot-scope="{object, onUpdate}" v-if="object">
            <div class="title is-7 has-text-centered">
                <a :href="`{{Request::url() }}/${object.id}`">
                    <span class="is-size-3" v-text="object.name"></span>
                    <font-awesome-icon icon="link"></font-awesome-icon>
                </a>
            </div>
            <dynamic-form :url="`{{Request::url() }}/edit/${object.id}`" :on-data-update="onUpdate"></dynamic-form>
        </template>
    @endif
</datatable>
