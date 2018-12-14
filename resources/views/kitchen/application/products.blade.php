@foreach(['menu','other'] as $category)
	<div class="field">
		<p class="title is-4">
			@lang("kitchen/products.{$category}"):
		</p>
		<dynamic-table :columns="[{
	name: 'name',
	label: '@lang('admin/applications.product')'
},{
	name: 'price',
	label: '@lang('admin/applications.price')',
	subType: 'number',
	type: 'text',
	icon: 'euro-sign',
	callbackOptions: {prefix: '€'},
	callback: 'localNumber|prefix'
}]" :init-fields="{{ $application->products()->where('category',$category)->get() }}"
					   @if($application->isOpen()) action="/kitchen/applications/{{$application->id}}/products" @endif
					   :extra-data="{category: '{{$category}}'}">
		</dynamic-table>
	</div>
	@if($errors->has($category))
		<p class="help is-danger">{{$errors->first($category)}}</p>
		@endif
	@if(! $loop->last)
		<hr>
	@endif
@endforeach