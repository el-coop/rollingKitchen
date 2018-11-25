@foreach(['food','drinks','other'] as $category)
	<div class="field">
		<p class="title is-4">
			@lang($category):
		</p>
		<dynamic-table :columns="[{
	name: 'name',
	label: '@lang('admin/applications.product')'
},{
	name: 'price',
	label: '@lang('admin/applications.price')',
	subType: 'number',
	type: 'text',
}]" :init-fields="{{ $application->products()->where('category',$category)->get() }}"
					   action="/kitchen/applications/{{$application->id}}/products"
					   :extra-data="{category: '{{$category}}'}">
		</dynamic-table>

	</div>
	@if(! $loop->last)
		<hr>
	@endif
@endforeach