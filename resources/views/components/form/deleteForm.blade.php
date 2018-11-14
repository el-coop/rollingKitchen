<form method="POST"
      action="{{$url}}">
    @csrf
    @method('DELETE')
    <div class="control">
        <button type="submit" class="button is-danger">
            @lang('global.delete')
        </button>
    </div>
</form>
