<div>
    <dynamic-table :columns="[{
                        name: 'name',
                        label: '@lang('global.name')'
                    },
                    {
                        name: 'email',
                        label: '@lang('global.email')'
                    },
                    {
                        name: 'language',
                        label: '@lang('global.language')',
                        type: 'select',
                        invisible: true,
                        options: {
                            en: '@lang('global.en')',
                            nl: '@lang('global.nl')'
                        }

                    }
        ]" :init-fields="{{$band->bandMembersForTable}}"
                   action="{{action('Band\BandController@addBandMember', $band)}}">
    </dynamic-table>
</div>