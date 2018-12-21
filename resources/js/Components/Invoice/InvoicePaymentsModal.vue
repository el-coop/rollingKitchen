<template>
	<modal-component name="payment" height="100%" :width="800" :pivotY="0"
					 :pivotX="1">
		<invoice-payments-table v-if="invoice.payments" @updated-total="updateInvoice" :columns="columns"
								:init-fields="invoice.payments"
								:action="action">
		</invoice-payments-table>
		<div v-else class="has-text-centered">
			<a class="button is-loading"></a>
		</div>
	</modal-component>
</template>

<script>
	import ModalComponent from '../Utilities/ModalComponent';
	import DynamicTable from '../Utilities/DynamicTable'
	import InvoicePaymentsTable from './InvoicePaymentsTable';

	export default {
		name: "InvoicePaymentsModal",
		components: {
			ModalComponent,
			DynamicTable,
			InvoicePaymentsTable
		},
		props: {
			fromUrl: {
				type: Boolean,
				default: false
			}
		},
		mounted() {
			this.$bus.$on('open-payment-modal', this.setUp);
		},
		beforeDestroy() {
			this.$bus.$off('open-payment-modal', this.setUp);
		},
		data() {
			return {
				invoice: [],
				columns: [
					{
						name: 'date',
						label: this.$translations.date,
						subType: 'date',
						callback: 'date'
					},
					{
						name: 'amount',
						label: this.$translations.amount,
						subType: 'number',
						callback: 'localNumber',

					},
				],
				onAdd: {}

			}
		},
		computed: {
			action() {
				return '/admin/invoices/payments/' + this.invoice.id;
			}
		},
		methods: {
			updateInvoice(total) {
				this.invoice.totalPaid = total;
				this.invoice.amountLeft = this.invoice.total - total;
				this.onAdd(this.invoice);
			},
			async setUp(field, onUpdate) {
				this.invoice = [];
				this.$modal.show('payment');
				if (this.fromUrl) {
					const response = await axios.get('/admin/invoices/payments/' + field.id);
					this.invoice = response.data;
				} else {
					this.invoice = field;
				}
				this.onAdd = onUpdate;
			}
		}
	}
</script>
