<div class="field">
    <p class="title is-4">
        @lang("band/band.setList"):
    </p>
    <dynamic-table :columns="[{
        name: 'title',
        label: '@lang('band/band.title')'
        },{
        name: 'composer',
        label: '@lang('band/band.composer')'
        },{
        name: 'owned',
        label: '@lang('band/band.owned')',
        type: 'select',
        options: [
        '@lang('vue.no')',
        '@lang('vue.yes')',
        ],
        callback: 'boolean'
        },{
        name: 'protected',
        label: '@lang('band/band.protected')',
        type: 'select',
        options: [
        '@lang('vue.no')',
        '@lang('vue.yes')',
        ],
        callback: 'boolean'

        }]" :init-fields="{{ $band->bandSongs }}" action="{{ action('Band\SongController@create', $band) }}">
    </dynamic-table>
    <hr>
    <div class="columns">
        <div class="column">
            <band-setlist-form band-id="{{ $band->id }}"
                           :init-setlist="{{ $band->setlistFile ?? '{}' }}"></band-setlist-form>

        </div>
        <div class="column"></div>
    </div>
    <hr>
    <div class="columns">
        <div class="column">
            <band-pdf-form band-id="{{ $band->id }}"
                           :init-has-pdf="{{ $band->pdf ? 'true' : 'false' }}"></band-pdf-form>

        </div>
        <div class="column"></div>
    </div>
</div>
