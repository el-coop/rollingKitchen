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
				<h4 class="title is-4">@lang('band/band.technicalRequirements')</h4>
				@if($band->pdf)
					<h6 class="subtitle is-6">
						<a href="{{action('Admin\BandController@showPdf', $band->pdf)}}">@lang('vue.download')</a>
					</h6>
				@endif
				<form method="post" enctype="multipart/form-data"
					  action="{{action('Band\BandController@uploadFile', $band)}}">
					@csrf
					<file-field :field="{
                     	name: 'file',
                        type: 'file',
                        label: '@lang('admin/settings.chooseFile')',
					}"></file-field>
					<button class="button is-success is-fullwidth">@lang('vue.upload')</button>
				</form>
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
