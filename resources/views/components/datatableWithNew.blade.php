@component('components.datatable')
    @slot('deleteButton', true)
    @slot('formattersData', $formattersData ?? null)
    @slot('customUrl', $customUrl ?? null)
    @slot('buttons')
        @isset($fieldType)
            <a class="button is-light"
               href="{{ action('Admin\FieldController@index', $fieldType) }}">@lang('admin/kitchens.fields')</a>
        @endisset
        <button class="button is-light" @click="actions.newObjectForm">@lang('vue.add')</button>
        @isset($buttons)
            @foreach($buttons as $button)
                {!! $button !!}
            @endforeach
        @endisset
    @endslot
    <template #default="{object, onUpdate}">
        <template v-if="object">
            <div class="title is-7 has-text-centered">
                @if($withEditLink ?? true)
                    <component :is="object.id ? 'a' : 'div'"
                               :href="object.id ? `{{Request::url() }}/${object.id}` : '#'">
                        <span class="is-size-3" v-text="object.name || '{{ $createTitle }}'"></span>
                        <font-awesome-icon icon="link" v-if="object.id"></font-awesome-icon>
                    </component>
                @else
                    <div>
							<span class="is-size-3"
                                  v-text="object.name || object.name_{{\App::getLocale()}} ||'{{ $createTitle }}'"></span>
                    </div>
                @endif
            </div>
            <dynamic-form :url="'{{Request::url() }}/edit' + (object.id ? `/${object.id}` : '')"
                          :on-data-update="onUpdate"
                          :method="object.id ? 'patch' : 'post'"
            ></dynamic-form>
            @isset($extraSlotView)
                @include($extraSlotView)
            @endisset
        </template>
    </template>
@endcomponent
