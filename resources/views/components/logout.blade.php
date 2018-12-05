<div class="navbar-item">
	<form action="{{ action('Auth\LoginController@logout') }}" method="post">
		@csrf
		<button type="submit" class="button is-danger {{ $class ?? 'is-inverted' }}">
			<span class="icon is-small">
			<font-awesome-icon icon="sign-out-alt" fixed-width></font-awesome-icon>
			</span>
			<span>@lang('global.logout')</span>
		</button>
	</form>
</div>
