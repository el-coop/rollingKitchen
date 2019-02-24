<form method="post" action="{{ action('Admin\WorkerController@disapprove') }}">
    @method('delete')
    @csrf
<button class="button is-light">Disapprove All</button>
</form>