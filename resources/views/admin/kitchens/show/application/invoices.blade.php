<dynamic-table :columns="[{
					name: 'number',
					label: '@lang('admin/applications.number')',
				},{
					name: 'amount',
					label: '@lang('admin/invoices.amount')',
					subtype: 'number',
					callback: 'localNumber',
				},{
					name: 'paid',
					label: '@lang('global.status')',
				}]" :init-fields="{{ $application->invoices }}"
			   action="{{ action('Admin\ApplicationInvoiceController@store', $application) }}" :modal="{
			   		width: 1000,
			   		height: '100%',
			   		pivotY: 0,
			   		pivotX: 1
			   }" :form-from-url="true" form-button-text="@lang('admin/invoices.send')">

</dynamic-table>