<div class="tile is-ancestor">
	<div class="tile is-parent">
		<div class="tile is-child">
			<h5 class="title is-5">{{ $worker->user->name }}</h5>
			<h6 class="subtitle is-6">
				<a href="{{ action('Admin\WorkerController@pdf', $worker) }}">Download PDF</a>
			</h6>
			<hr>
			<dynamic-form :init-fields="{{ $worker->fulldata }}"
						  url="{{ action('Admin\WorkerController@update', $worker) }}"></dynamic-form>
		</div>
	</div>
	<div class="tile is-parent is-vertical">
		<div class="tile is-child">
			<p class="title">
				@lang('global.photos')
			</p>
			<carousel :photos="{{ $worker->photos }}">

			</carousel>
		</div>
	</div>
</div>
