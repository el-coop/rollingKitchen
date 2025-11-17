<div>
    <p class="title is-4">
        @lang('kitchen/products.menuHeader'):
    </p>
    @component('kitchen.application.applicationMenuTable', [
    'menuTable' => $application->menuTable,
    'application' => $application,
    ])
    @endcomponent
</div>
<div class="field">
    <p class="title is-4">
        @lang("kitchen/products.other"):
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
}]" :init-fields="{{ $application->products->where('category','other')->values() }}"
                   @can('update',$application) action="/kitchen/applications/{{$application->id}}/product"
                   @endcan
                   :extra-data="{category: 'other'}">
    </dynamic-table>
</div>
@if($errors->has('other'))
    <p class="help is-danger">{{$errors->first('other')}}</p>
@endif
<hr>

