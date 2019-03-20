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
        ]" :init-fields="{{$band->schedulesForTable->values()}}">
        <template #actions="{field, onUpdate}">
            <div v-if="field.approved === 'pending'">
                <approve-schedule :on-update="onUpdate" :object="field" :band="{{$band->id}}">

                </approve-schedule>
            </div>
            <div v-else v-text="$translations[field.approved]">
            </div>
        </template>
    </dynamic-table>
</div>