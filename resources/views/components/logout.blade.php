<div class="navbar-item">
	<form action="{{ action('Auth\LoginController@logout') }}" method="post">
		@csrf
		<button type="submit" class="button is-danger {{ $class ?? 'is-inverted' }}">
			<font-awesome-icon icon="sign-out-alt" class="icon" fixed-width></font-awesome-icon>
		</button>
	</form>
</div>
