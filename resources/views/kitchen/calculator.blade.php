
<fee-calculator :service-total="{{$kitchen->servicesTotal}}">
    <template #services>
        <div class="box">
            <label class="label">@lang('admin/services.services')</label>
            <dynamic-table :columns="[{
    name: 'service',
    label: '@lang('global.name')'
    },{
    name: 'price',
    label: '@lang('admin/invoices.amount')',
    },{
    name: 'amount',
    label: '@lang('admin/invoices.number')',
    responsiveHidden: true,
    },{
    name: 'total',
    label: '@lang('admin/invoices.amount')',

    }]"
                           :init-fields="{{json_encode($kitchen->servicesCalculationTable)}}" :edit="false"
                           :delete-allowed="false">
            </dynamic-table>
            <div class="is-flex is-justify-content-end">
                <label class="label">@lang('vue.total'):
                    € {{number_format($kitchen->servicesTotal,2,$decimalPoint,$thousandSeparator)}}</label>
            </div>
        </div>
    </template>
</fee-calculator>
