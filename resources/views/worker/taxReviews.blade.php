<dynamic-table :columns="[{
	name: 'name',
	label: '@lang('global.name')'
},{
    name: 'file',
    label: '@lang('admin/settings.chooseFile')',
    type: 'file',
    invisible: true,
    edit: false
}]" :init-fields="{{$worker->taxReviews}}" action="{{action('Admin\WorkerController@addTaxReview', $worker)}}"
			   :headers="{'Content-Type': 'multipart/form-data'}">
</dynamic-table>
