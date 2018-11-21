<dynamic-table :columns="[{
	name: 'name',
	label: '@lang('admin/applications.product')'
},{
	name: 'price',
	label: '@lang('admin/applications.price')',
	type: 'number'
}]" :init-fields="{{ $application->products }}" action="/kitchen/applications/{{$application->id}}/products">

</dynamic-table>