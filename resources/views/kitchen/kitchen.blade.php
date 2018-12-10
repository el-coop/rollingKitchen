<div class="columns">
	<div class="column">
		<dynamic-fields :fields="{{ $kitchen->fulldata->map(function($item) use($errors){
	$fieldName = str_replace(']','',str_replace('[','.',$item['name']));

	$item['value'] = old($fieldName, $item['value']);
	$item['error'] = $errors->has($fieldName) ? $errors->get($fieldName): null;
	return $item;
}) }}" :hide="['status','kitchen[6]','kitchen[7]','kitchen[11]']"></dynamic-fields>
	</div>
	<div class="column">
		<p class="title is-4"></p>
		<image-manager url="{{ action('Kitchen\KitchenController@storePhoto', $kitchen) }}" :data="{
			_token: '{{csrf_token()}}'
		}" :init-images="{{ $kitchen->photos }}" delete-url="/kitchen/{{ $kitchen->id }}/photo">
		</image-manager>
		<dynamic-fields class="mt-1" :fields="{{ $kitchen->fulldata->map(function($item) use($errors){
			$fieldName = str_replace(']','',str_replace('[','.',$item['name']));

			$item['value'] = old($fieldName, $item['value']);
			$item['error'] = $errors->has($fieldName) ? $errors->get($fieldName): null;
			return $item;
		}) }}"
						:hide="['status','name','language','email','kitchen[1]','kitchen[2]','kitchen[3]','kitchen[4]','kitchen[5]']"></dynamic-fields>
	</div>
</div>
