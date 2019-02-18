<dynamic-table :columns="[{
	name: 'name',
	label: '@lang('global.name')'
},{
    name: 'file',
    label: '@lang('admin/settings.chooseFile')',
    type: 'file',
    invisible: true,
    edit: false
}]" :init-fields="{{$worker->taxReviews}}"
			   @can('taxReview',$worker) action="{{action('Admin\WorkerController@storeTaxReview', $worker)}}"
			   @endcan
			   :headers="{'Content-Type': 'multipart/form-data'}">
	<template #actions="{field}">
		<a class="is-link" :href="field.url">@lang('vue.download')</a>
	</template>
</dynamic-table>
