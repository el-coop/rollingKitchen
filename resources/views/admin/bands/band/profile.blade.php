<div>
    <form method="post" action="{{action('Admin\BandController@nonAjaxUpdate', $band)}}">
        @method('patch')
        @csrf
        <dynamic-fields :fields="{{ $band->fulldata->map(function($item) use($errors){
	$fieldName = str_replace(']','',str_replace('[','.',$item['name']));

	$item['value'] = old($fieldName, $item['value']);
	$item['error'] = $errors->has($fieldName) ? $errors->get($fieldName): null;
	return $item;
}) }}" class="mb-1"></dynamic-fields>
        <button class="button is-success">@lang('global.save')</button>
    </form>
</div>