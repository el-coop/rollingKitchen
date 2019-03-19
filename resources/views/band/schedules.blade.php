<div>
    <dynamic-table :columns="[{
                        name: 'stage',
                        label: '@lang('admin/artists.stages')'
                    },
                    {
                        name: 'dateTime',
                        label: '@lang('vue.date')'
                    },
                    {
                        name: 'payment',
                        label: '@lang('vue.budget')',
                        callback: 'localNumber'
                    }
        ]" :init-fields="{{$band->pendingSchedule->values()}}">
        <template #actions="{field, approve}">
            <div class="columns">
                <button @click="approve(['reject',field, {{$band->id}}])" class="button is-danger">@lang('band/band.reject')</button>
                <button @click="approve(['approve',field, {{$band->id}}])" class="button is-info">@lang('band/band.approve')</button>
            </div>
        </template>
    </dynamic-table>
</div>