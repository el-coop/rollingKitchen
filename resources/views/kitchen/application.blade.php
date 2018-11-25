<div class="columns">
	<div class="column">
		@include('kitchen.application.dimensions')
		<dynamic-fields :fields="{{ $application->fulldata->map(function($item) use($errors){
	$fieldName = str_replace(']','',str_replace('[','.',$item['name']));

	$item['value'] = old($fieldName, $item['value']);
	$item['error'] = $errors->has($fieldName) ? $errors->get($fieldName): null;
	return $item;
}) }}" :hide="['year','status']"></dynamic-fields>
	</div>
	<div class="column">
		@include('kitchen.application.products')
	</div>
</div>
