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
			   @if(\Auth::user()->user_type == \App\Models\Admin::class) action="{{action('Admin\WorkerController@storeTaxReview', $worker)}}"
			   @endif
			   :headers="{'Content-Type': 'multipart/form-data'}">
	<template slot="actions" slot-scope="{field}">
		<a class="is-link" :href="field.url">@lang('vue.download')</a>
	</template>
</dynamic-table>
