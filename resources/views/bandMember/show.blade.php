@extends('layouts.site')

@section('title',$bandMember->user->name)

@section('content')
	<div class="box">
		<form method="post" action="{{action('BandMember\BandMemberController@update', $bandMember)}}">
			@method('patch')
			@csrf
			<div class="columns">
				<div class="column">
					<dynamic-fields :fields="{{ $bandMember->fulldata->map(function($item) use($errors){
	$fieldName = str_replace(']','',str_replace('[','.',$item['name']));

	$item['value'] = old($fieldName, $item['value']);
	$item['error'] = $errors->has($fieldName) ? $errors->get($fieldName): null;
	return $item;
}) }}" class="mb-1" :hide="['payment']"></dynamic-fields>
				</div>
				<div class="column">
					<h4 class="title is-4">@lang('worker/worker.uploadId')</h4>
					<h5 class="subtitle is-6">@lang('worker/worker.uploadInstructions')</h5>
					<image-manager url="{{ action('BandMember\BandMemberController@storePhoto', $bandMember) }}" :data="{
						_token: '{{csrf_token()}}'
					}"
								   :init-images="{{ $bandMember->photos }}" delete-url="/bandMember/{{ $bandMember->id }}/photo">
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
				@if(!$bandMember->submitted)
					<confirmation-submit label="@lang('kitchen/kitchen.submitReview')"
										 title="@lang('kitchen/kitchen.submitConfirmTitle')"
										 subtitle="{{$privacyStatement}}"
										 yes-text="@lang('global.yes')"
										 no-text="@lang('global.no')" name="review" value="1"
										 id="reviewButton"></confirmation-submit>
				@endif
			</div>
		</form>
	</div>
@endsection
