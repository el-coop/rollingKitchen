<datatable :field-settings="{{ collect($fields) }}"
           :extra-params="{
		   		table: '{{collect($table)}}'
		   }"
           @isset($editWidth)
           :edit-width="{{$editWidth}}"
           @endisset
           url="\{{$url}}"
           :labels="{
		   		pagination: '@lang('datatable.pagination')',
		   		noPagination: '@lang('datatable.noPagination')',
		   		next: '@lang('datatable.next')',
		   		prev: '@lang('datatable.prev')',
		   		filters: '@lang('datatable.filters')',
		   		filter: '@lang('datatable.filter')',
		   		clear: '@lang('datatable.clear')',
		   }"
           :init-filters="{{ $filters ?? '{}' }}"
           @isset($deleteButton)
           :delete-slot="true"
           @endif
           @isset($formattersData)
           :formatters-data="{{$formattersData}}"
           @endif
           @isset($deleteButtonTxt)
           delete-btn="{{$deleteButtonTxt}}"
        @endisset
>
    @isset($buttons)
        <template slot="buttons" slot-scope="{actions}">{{$buttons}}</template>
    @endisset
    @if(trim($slot) !== '')
        {{ $slot }}
    @else
        <template slot-scope="{object, onUpdate}" v-if="object">
            <div class="title is-7 has-text-centered">
                <a :href="`{{Request::url() }}/${object.id}`">
                    <span class="is-size-3" v-text="object.name"></span>
                    <font-awesome-icon icon="link"></font-awesome-icon>
                </a>
            </div>
            <dynamic-form :url="`{{Request::url() }}/edit/${object.id}`" :on-data-update="onUpdate"></dynamic-form>
        </template>
    @endif
</datatable>