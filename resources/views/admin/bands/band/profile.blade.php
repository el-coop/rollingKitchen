<div>
	<h5 class="title is-5">{{ $band->user->name }}</h5>
	@if($band->pdf)
		<h6 class="subtitle is-6">
			<a href="{{action('Admin\BandController@showPdf', $band->pdf)}}">@lang('band/band.technicalRequirements')</a>
		</h6>
	@endif
	<hr>
	<form method="post" action="{{action('Admin\BandController@nonAjaxUpdate', $band)}}">
		@method('patch')
		@csrf
		<dynamic-fields :fields="{{ $band->fulldata->map(function($item) use($errors){
	$fieldName = str_replace(']','',str_replace('[','.',$item['name']));

	$item['value'] = old($fieldName, $item['value']);
	$item['error'] = $errors->has($fieldName) ? $errors->get($fieldName): null;
	return $item;
}) }}" class="mb-1"></dynamic-fields>
		<button class="button is-success">@lang('global.save')</button>
	</form>
</div>
