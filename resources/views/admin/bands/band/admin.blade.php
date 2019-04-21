<div class="tile is-ancestor">
    <div class="tile is-parent">
        <div class="tile is-child">
            <form method="post" action="{{action('Admin\BandController@updateAdmin', $bandAdmin)}}">
                @method('patch')
                @csrf
                <dynamic-fields :fields="{{ $bandAdmin->fulldata->map(function($item) use($errors){
	$fieldName = str_replace(']','',str_replace('[','.',$item['name']));

	$item['value'] = old($fieldName, $item['value']);
	$item['error'] = $errors->has($fieldName) ? $errors->get($fieldName): null;
	return $item;
}) }}" class="mb-1"></dynamic-fields>
                <div class="buttons has-content-justified-center">
                    <button class="button is-link">
                        @lang('global.save')
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="tile is-parent is-vertical">
        <div class="tile is-child">
            <p class="title">
                @lang('global.photos')
            </p>
            <carousel :photos="{{ $bandAdmin->photos }}">

            </carousel>
        </div>
    </div>
</div>