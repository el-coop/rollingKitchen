<div>
	@if($errors->has('tracks'))
		<p class="has-text-danger mb-1">
			@lang('band/band.setListError')
		</p>
	@endif
	<form method="post" action="{{action('Band\BandController@update', $band)}}">
		@method('patch')
		@csrf
		<dynamic-fields :fields="{{ $band->fulldata->map(function($item) use($errors){
	$fieldName = str_replace(']','',str_replace('[','.',$item['name']));

	$item['value'] = old($fieldName, $item['value']);
	$item['error'] = $errors->has($fieldName) ? $errors->get($fieldName): null;
	return $item;
}) }}" class="mb-1"></dynamic-fields>
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
