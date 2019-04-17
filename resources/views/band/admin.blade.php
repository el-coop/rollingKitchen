<div>
    <form method="post" action="{{action('Band\BandAdminController@update',[$band, $band->admin])}}">
        @method('patch')
        @csrf
        <div class="columns">
            <div class="column">
                <dynamic-fields :fields="{{ $band->admin->fulldata->map(function($item) use($errors){
	$fieldName = str_replace(']','',str_replace('[','.',$item['name']));

	$item['value'] = old($fieldName, $item['value']);
	$item['error'] = $errors->has($fieldName) ? $errors->get($fieldName): null;
	return $item;
}) }}" class="mb-1" :hide="['payment']"></dynamic-fields>
            </div>
            <div class="column">
                <h4 class="title is-4">@lang('worker/worker.uploadId')</h4>
                <h5 class="subtitle is-6">@lang('worker/worker.uploadInstructions')</h5>
                <image-manager url="{{ action('Band\BandAdminController@storePhoto', [$band,$band->admin]) }}" :data="{
						_token: '{{csrf_token()}}'
					}"
                               :init-images="{{ $band->admin->photos }}"
                               delete-url="/admin/{{ $band->admin->id }}/photo">
                </image-manager>
                @if($errors->has('photos'))
                    <p class="help is-danger">{{$errors->first('photos')}}</p>
                @endif
            </div>
        </div>
        <div class="buttons has-content-justified-center">
            <button class="button is-link">
                @lang('global.save')
            </button>
        </div>
    </form>
</div>
</div>