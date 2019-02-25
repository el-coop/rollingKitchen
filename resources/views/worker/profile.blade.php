<div class="columns">
	<div class="column">
		<form method="post" action="{{ action('Worker\WorkerController@update', $worker) }}">
			@method('patch')
			@csrf
			<dynamic-fields :fields="{{ $worker->fulldata->map(function($item) use($errors){
	$fieldName = str_replace(']','',str_replace('[','.',$item['name']));

	$item['value'] = old($fieldName, $item['value']);
	$item['error'] = $errors->has($fieldName) ? $errors->get($fieldName): null;
	return $item;
}) }}" class="mb-1" :hide="{{ collect(['supervisor','approved','workplaces']) }}"></dynamic-fields>
			<button class="button is-success">@lang('global.save')</button>
		</form>
	</div>
	<div class="column">
		<h4 class="title is-4">@lang('worker/worker.uploadId')</h4>
		<image-manager url="{{ action('Worker\WorkerController@storePhoto', $worker) }}" :data="{
			_token: '{{csrf_token()}}'
		}" :init-images="{{ $worker->photos }}" delete-url="/worker/{{ $worker->id }}/photo">
		</image-manager>
	</div>
</div>
