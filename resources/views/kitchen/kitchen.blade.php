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
		<dynamic-fields :fields="{{ $kitchen->fulldata->whereIn('name',['kitchen[6]','kitchen[7]','kitchen[11]'])->map(function($item) use($errors){
			$fieldName = str_replace(']','',str_replace('[','.',$item['name']));

			$item['value'] = old($fieldName, $item['value']);
			$item['error'] = $errors->has($fieldName) ? $errors->get($fieldName): null;
			return $item;
		})->values() }}"></dynamic-fields>
		<dynamic-fields class="mt-1" :fields="{{ collect([[
			'name' => 'story',
			'label' => __('kitchen/kitchen.story'),
			'value' => old('story', $application->story),
			'readonly' => !$application->isOpen(),
			'type' => 'textarea',
			'error' => $errors->has('story') ? $errors->get('story') : null,
		]]) }}"></dynamic-fields>
	</div>
</div>
