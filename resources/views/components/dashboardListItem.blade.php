<list-section label="{{$label}}" :start-open="{{ $items->first(function($link){
	$link = action($link,[],false);
	return Request::is(trim($link,'/'));
}) ? 'true' : 'false'}}">
	@foreach($items as $text => $link)
		@php
			$link = action($link,[],false);
		@endphp
		<li>
			<a href="{{$link }}"
			   class="{{Request::is(trim($link,'/')) ? 'is-active' : '' }}"
			>{{$text}}</a>
		</li>
	@endforeach
</list-section>
