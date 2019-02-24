<form method="post" action="{{ action('Admin\ShiftController@deleteAll') }}">
    @method('delete')
    @csrf
    <button class="button is-light">Delete All</button>
</form>