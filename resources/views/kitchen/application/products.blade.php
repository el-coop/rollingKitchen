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
    <yes-no-tooltip-field :field="{{ collect([
        'name' => ($readonly ?? false) ? "sells_drinks_{$application->id}" : 'sells_drinks',
        'label' => __('kitchen/products.other') . ':',
        'value' => ($readonly ?? false) ? $application->sells_drinks : old('sells_drinks', $application->sells_drinks),
        'disabled' => ($readonly ?? false) || !$application->isOpen(),
        'alwaysShowContent' => ($readonly ?? false) && $application->products->where('category', 'other')->isNotEmpty(),
        'yes' => __('global.yes'),
        'no' => __('global.no'),
        'tooltip' => app('settings')->get('application_drinks_popup_' . App::getLocale()),
    ]) }}" :error="{{ $errors->has('sells_drinks') ? collect($errors->get('sells_drinks')) : 'null' }}">
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
                   @can('update',$application) action="/kitchen/applications/{{$application->id}}/products"
                   @endcan
                   :extra-data="{category: 'other'}">
    </dynamic-table>
    </yes-no-tooltip-field>
</div>
@if($errors->has('other'))
    <p class="help is-danger">{{$errors->first('other')}}</p>
@endif
<hr>

