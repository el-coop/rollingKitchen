<form method="post" action="{{action('Kitchen\KitchenController@usePastApplication', $application)}}">
    @csrf
    @method('patch')
    <p class="title is-4">
        @lang('kitchen/kitchen.usePastApplication')
    </p>
    <div class="field">
        <label class="label">@lang('kitchen\kitchen.usePastApplication')</label>
        <div class="control">
            <div class="select is-fullwidth">
                <select name="pastApplication">
                    @foreach($pastApplications as $pastApplication)
                        <option value="{{$pastApplication->id}}">{{$pastApplication->year}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="buttons">
        <button class="button is-fullwidth is-primary"
                type="submit">
            @lang('kitchen\kitchen.fill')
        </button>
    </div>
</form>