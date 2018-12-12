<div class="title is-4">@lang('admin/invoices.invoices')</div>
<dynamic-table :columns="[{
					name: 'formattedNumber',
					label: '@lang('admin/invoices.number')',
				},{
					name: 'total',
					label: '@lang('admin/invoices.amount')',
					subtype: 'number',
					callback: 'localNumber',
				},{
					name: 'paid',
					label: '@lang('global.status')',
					callback: 'paidStatus'
				}]" :init-fields="{{ $debtor->invoices }}"
			   action="{{ action('Admin\DebtorInvoiceController@create', $debtor) }}" :modal="{
			   		width: 1000,
			   		height: '100%',
			   		pivotY: 0,
			   		pivotX: 1
			   }" :form-from-url="true" form-button-text="@lang('admin/invoices.send')" :delete-allowed="false">
</dynamic-table>