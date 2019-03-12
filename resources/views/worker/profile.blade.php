<form method="post" action="{{ action('Worker\WorkerController@update', $worker) }}">
	<div class="columns">
		<div class="column">
			@method('patch')
			@csrf
			<dynamic-fields :fields="{{ $worker->fulldata->map(function($item) use($errors){
					$fieldName = str_replace(']','',str_replace('[','.',$item['name']));

					$item['value'] = old($fieldName, $item['value']);
					$item['error'] = $errors->has($fieldName) ? $errors->get($fieldName): null;
					return $item;
				}) }}"
							class="mb-1"
							:hide="{{ collect(['supervisor','approved','workplaces'])->concat($rightSideFields) }}">
			</dynamic-fields>
		</div>
		<div class="column">
			<h4 class="title is-4">@lang('worker/worker.uploadId')</h4>
			<image-manager url="{{ action('Worker\WorkerController@storePhoto', $worker) }}" :data="{
					_token: '{{csrf_token()}}'
				}"
						   :init-images="{{ $worker->photos }}" delete-url="/worker/{{ $worker->id }}/photo">
			</image-manager>
			@if($errors->has('photos'))
				<p class="help is-danger">{{$errors->first('photos')}}</p>
			@endif
			<dynamic-fields :fields="{{ $rightSideFields->map(function ($field) use ($worker, $errors) {
					$item = $worker->fullData->firstWhere('name', $field);

					$fieldName = str_replace(']','',str_replace('[','.',$item['name']));

					$item['value'] = old($fieldName, $item['value']);
					$item['error'] = $errors->has($fieldName) ? $errors->get($fieldName): null;
					return $item;
				}) }}"
							class="mb-1">
			</dynamic-fields>
		</div>
	</div>
	<div class="buttons has-content-justified-center">
		<button class="button is-link">
			@lang('global.save')
		</button>
		@if(!$worker->submitted)
			<confirmation-submit label="@lang('kitchen/kitchen.submitReview')"
								 title="@lang('kitchen/kitchen.submitConfirmTitle')"
								 subtitle="{{ $privacyStatement }}"
								 yes-text="@lang('global.yes')"
								 no-text="@lang('global.no')" name="review" value="1"
								 id="reviewButton"></confirmation-submit>
		@endif
	</div>
</form>
