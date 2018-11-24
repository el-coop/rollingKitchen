<div class="columns">
	<div class="column">
		<dynamic-fields :fields="{{ $kitchen->fulldata->map(function($item) use($errors){
	$fieldName = str_replace(']','',str_replace('[','.',$item['name']));

	$item['value'] = old($fieldName, $item['value']);
	$item['error'] = $errors->has($fieldName) ? $errors->get($fieldName): null;
	return $item;
}) }}" :hide="['status']"></dynamic-fields>
	</div>
	<div class="column">
		<p class="title is-4"></p>
		<image-manager url="{{ action('Kitchen\KitchenController@storePhoto', $kitchen) }}" :data="{
			_token: '{{csrf_token()}}'
		}" :init-images="{{ $kitchen->photos }}" delete-url="/kitchen/{{ $kitchen->id }}/photo">
		</image-manager>
	</div>
</div>
