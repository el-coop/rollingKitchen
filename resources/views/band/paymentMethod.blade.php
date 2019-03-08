<div>
    <form method="post" action="{{action('Band\BandController@updatePaymentMethod', $band)}}">
        @method('patch')
        @csrf
        <select-field
                :field="{label: '@lang('band/band.paymentMethod')', value: '{{$band->payment_method}}',  name: 'paymentMethod', options: {band: '@lang('admin/fields.Band')', individual: '@lang('band/band.individual')'}}">
        </select-field>
        <button class="button is-success">@lang('global.save')</button>
    </form>
    @if($band->payment_method == 'individual')
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
        ]" :init-fields="{{$band->bandMembersForTable}}" action="{{action('Band\BandController@addBandMember', $band)}}">
        </dynamic-table>
    @endif
</div>