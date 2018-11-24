@component('components.card',[
					'class' => 'h-100'
				])
	<div class="h-100 is-flex has-content-justified-center has-items-aligned-center">
		<figure class="image is-square" style="width: 100%">
			<img src="{{ asset('images/logo.png') }}" alt="@lang('global.title')">
		</figure>
	</div>
@endcomponent