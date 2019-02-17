<dynamic-table :columns="[{
					name: 'formattedNumber',
					label: '@lang('admin/invoices.number')',
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
				}]" :init-fields="{{ $application->invoices }}"
			   action="{{ action('Admin\ApplicationInvoiceController@store', $application) }}" :modal="{
			   		width: 1000,
			   		height: '100%',
			   		pivotY: 0,
			   		pivotX: 1
			   }" :form-from-url="true" form-button-text="@lang('admin/invoices.send')" :delete-allowed="false">
	<template #actions="{field, onUpdate}">
		<button @click="$bus.$emit('open-payment-modal', field, onUpdate)" class="button is-success">@lang('admin/invoices.managePayments')</button>
	</template>
</dynamic-table>
<invoice-payments-modal>

</invoice-payments-modal>

