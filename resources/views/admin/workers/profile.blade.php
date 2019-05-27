<div class="tile is-ancestor">
	<div class="tile is-parent">
		<div class="tile is-child">
			<h5 class="title is-5">{{ $worker->user->name }}</h5>
			<h6 class="subtitle is-6">
				<a href="{{ action('Admin\WorkerController@pdf', $worker) }}">Download PDF</a>
			</h6>
			<hr>
			<form method="post" action="{{action('Admin\WorkerController@nonAjaxUpdate', $worker)}}">
				@csrf
				@method('patch')
				<dynamic-fields :fields="{{ $worker->fulldata->map(function($item) use($errors){
					$fieldName = str_replace(']','',str_replace('[','.',$item['name']));

					$item['value'] = $item['type'] == 'multiselect' ? $item['value']: old($fieldName, $item['value']);
					$item['error'] = $errors->has($fieldName) ? $errors->get($fieldName): null;
					return $item;
				}) }}" class="mb-1">
				</dynamic-fields>
				<button class="button is-fullwidth is-success">
					@lang('global.save')
				</button>
			</form>
		</div>
	</div>
	<div class="tile is-parent is-vertical">
		<div class="tile is-child">
			<p class="title">
				@lang('global.photos')
			</p>
			<image-manager url="{{ action('Worker\WorkerController@storePhoto', $worker) }}" :data="{
					_token: '{{csrf_token()}}'
				}" :init-images="{{ $worker->photos }}" delete-url="/worker/{{ $worker->id }}/photo">
				<template #display="{images}">
					<carousel :photos="images">
					</carousel>
				</template>
			</image-manager>
		</div>
	</div>
</div>
