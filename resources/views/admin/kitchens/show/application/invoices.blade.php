<dynamic-table :columns="[{
    name: 'formattedNumber',
    label: '@lang('admin/invoices.number')',
    responsiveHidden: true,
    },{
    name: 'total',
    label: '@lang('admin/invoices.amount')',
    subType: 'number',
    callback: 'localNumber',
    },{
    name: 'totalPaid',
    label: '@lang('admin/invoices.totalPaid')',
    subType: 'number',
    callback: 'localNumber',
    },{
    name: 'amountLeft',
    label: '@lang('admin/invoices.amountLeft')',
    subType: 'number',
    callback: 'localNumber',
    responsiveHidden: true,
    }]" :init-fields="{{ $application->invoices }}"
               action="{{ action('Admin\ApplicationInvoiceController@store', $application) }}" :modal="{
			   		width: 1000,
			   		height: '100%',
			   		pivotY: 0,
			   		pivotX: 1
			   }" :form-from-url="true" :delete-allowed="false">
    <template #actions="{field, onUpdate}">
        <invoice-payments-modal #default="{open}" :field="field" :on-update="onUpdate">
            <button @click="open" class="button is-success">@lang('admin/invoices.managePayments')</button>
        </invoice-payments-modal>
    </template>
</dynamic-table>
