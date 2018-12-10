<dynamic-table :columns="[{
					name: 'formattedNumber',
					label: '@lang('admin/invoices.number')',
				},{
					name: 'total',
					label: '@lang('admin/invoices.amount')',
					subType: 'number',
					callback: 'localNumber',
				},{
					name: 'paid',
					label: '@lang('global.status')',
					callback: 'paidStatus'
				}]" :init-fields="{{ $application->invoices()->with('payments')->get() }}"
			   action="{{ action('Admin\ApplicationInvoiceController@store', $application) }}" :modal="{
			   		width: 1000,
			   		height: '100%',
			   		pivotY: 0,
			   		pivotX: 1
			   }" :form-from-url="true" form-button-text="@lang('admin/invoices.send')" :delete-allowed="false">
	<template slot="actions" slot-scope="{field, onUpdate}">
		<button @click="$bus.$emit('open-payment-modal', field)" class="button is-success">@lang('admin/invoices.addPayment')</button>
	</template>
</dynamic-table>
<invoice-table>
</invoice-table>
