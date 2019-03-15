@extends('layouts.site')

@section('title',$bandMember->user->name)

@section('content')
    <div class="box">
        <form method="post" action="{{action('BandMember\BandMemberController@update', $bandMember)}}">
            @method('patch')
            @csrf
            <dynamic-fields :fields="{{ $bandMember->fulldata->map(function($item) use($errors){
	$fieldName = str_replace(']','',str_replace('[','.',$item['name']));

	$item['value'] = old($fieldName, $item['value']);
	$item['error'] = $errors->has($fieldName) ? $errors->get($fieldName): null;
	return $item;
}) }}" class="mb-1"></dynamic-fields>
            <div class="buttons has-content-justified-center">
                <button class="button is-link">
                    @lang('global.save')
                </button>
                @if(!$bandMember->submitted)
                    <confirmation-submit label="@lang('kitchen/kitchen.submitReview')"
                                         title="@lang('kitchen/kitchen.submitConfirmTitle')"
                                         subtitle="@lang('kitchen/kitchen.submitConfirmSubtitle')"
                                         yes-text="@lang('global.yes')"
                                         no-text="@lang('global.no')" name="review" value="1"
                                         id="reviewButton"></confirmation-submit>
                @endif
            </div>
        </form>
    </div>
@endsection