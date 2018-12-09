<div class="notification">
	{!!  str_replace(PHP_EOL,'<br>',$message) !!}
	<hr>
	<ul>
		@foreach($pdfs as $pdf)
			<li>{{$pdf->name}}</li>
		@endforeach
	</ul>
</div>