<div class="tile is-ancestor">
	<div class="tile is-parent">
		<div class="tile is-child">
			<dynamic-form :init-fields="{{ $kitchen->fulldata }}"
						  url="{{ action('Admin\KitchenController@update', $kitchen) }}"></dynamic-form>
		</div>
	</div>
	<div class="tile is-parent is-vertical">
		<div class="tile is-child">
			<p class="title">
				@lang('global.photos')
			</p>
			<carousel :photos="{{ $kitchen->photos }}">

			</carousel>
		</div>
	</div>
</div>