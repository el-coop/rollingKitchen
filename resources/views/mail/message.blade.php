@component('mail::layout')
	{{-- Header --}}
	@slot('header')
		@component('mail::header', ['url' => config('app.url')])
			<img src="{{asset('storage/images/logo.png')}}" style="height: 32px; max-height: 32px">
			<br> {{ config('app.name') }}
		@endcomponent
	@endslot

	{{-- Body --}}
	{{ $slot }}

	{{-- Subcopy --}}
	@isset($subcopy)
		@slot('subcopy')
			@component('mail::subcopy')
				{{ $subcopy }}
			@endcomponent
		@endslot
	@endisset

	{{-- Footer --}}
	@slot('footer')
		@component('mail::footer')
			© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
		@endcomponent
	@endslot
@endcomponent
