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
				}]" :init-fields="{{ $application->invoices }}"
			   action="{{ action('Admin\ApplicationInvoiceController@store', $application) }}" :modal="{
			   		width: 1000,
			   		height: '100%',
			   		pivotY: 0,
			   		pivotX: 1
			   }" :form-from-url="true" form-button-text="@lang('admin/invoices.send')" :delete-allowed="false">
	<template slot="actions" slot-scope="{field, onUpdate}">
		<dynamic-form
				:button-text="field.paid ? '@lang('admin/invoices.toggleUnpaid')' : '@lang('admin/invoices.togglePaid')'"
				:init-fields="[]"
				:url="`/admin/invoices/${field.id}/toggle`"
				:button-class="field.paid ? 'is-danger' : 'is-success'"
				:on-data-update="onUpdate"
				successToast="@lang('admin/invoices.invoiceSent')">
		</dynamic-form>
	</template>
</dynamic-table>