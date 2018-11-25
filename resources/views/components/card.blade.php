<div class="card is-flex is-column {{$class ?? ''}}">
	@isset($title)
		<div class="card-header">
			<div class="card-header-title">
				{{$title}}
			</div>
		</div>
	@endisset
	<div class="card-content fill-parent">
		{{$slot}}
	</div>
</div>