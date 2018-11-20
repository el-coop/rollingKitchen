<list-section label="{{__($label)}}" :start-open="{{ $items->first(function($link) use ($indexLink){
	$link = action($link,[],false);
	return Request::is(trim($link,'/')) || $link == $indexLink;
}) ? 'true' : 'false'}}">
	@foreach($items as $linkLabel => $link)
		@php
			$link = action($link,[],false);
		@endphp
		<li>
			<a href="{{$link }}"
			   class="{{Request::is(trim($link,'/')) ||  $link == $indexLink ? 'is-active' : '' }}"
			>{{__($linkLabel)}}</a>
		</li>
	@endforeach
</list-section>
