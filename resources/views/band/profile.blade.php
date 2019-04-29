<div>
    @if($errors->has('tracks'))
        <p class="has-text-danger mb-1">
            @lang('band/band.setListError')
        </p>
    @endif
    <form method="post" action="{{action('Band\BandController@update', $band)}}">
        @method('patch')
        @csrf
        <div class="columns">
            <div class="column">
                <dynamic-fields :fields="{{ $band->fulldata->map(function($item) use($errors){
	$fieldName = str_replace(']','',str_replace('[','.',$item['name']));

	$item['value'] = old($fieldName, $item['value']);
	$item['error'] = $errors->has($fieldName) ? $errors->get($fieldName): null;
	return $item;
}) }}" class="mb-1"></dynamic-fields>
            </div>
            <div class="column">
                <dynamic-form :init-fields="[
                    {
                        name: 'file',
                        type: 'file',
                        label: '@lang('admin/settings.chooseFile')',
                    }
                ]" method="post" :headers="{'Content-Type': 'multipart/form-data'}" button-text="@lang('vue.upload')" url="{{action('Band\BandController@uploadFile', $band)}}">

                </dynamic-form>
            </div>
        </div>

        <div class="buttons has-content-justified-center">
            <button class="button is-link">
                @lang('global.save')
            </button>
            @if(!$band->submitted)
                <confirmation-submit label="@lang('kitchen/kitchen.submitReview')"
                                     title="@lang('kitchen/kitchen.submitConfirmTitle')"
                                     subtitle=" "
                                     yes-text="@lang('global.yes')"
                                     no-text="@lang('global.no')" name="review" value="1"
                                     id="reviewButton"></confirmation-submit>
            @endif
        </div>
    </form>
</div>
