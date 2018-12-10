<template>
    <modal-component name="payment" height="100%" :width="800" :pivotY="0"
                     :pivotX="1">
        <dynamic-table :columns="columns" :init-fields="invoice.payments" :action="action">

        </dynamic-table>
    </modal-component>
</template>

<script>
    import ModalComponent from '../Utilities/ModalComponent';
    import DynamicTable from '../Utilities/DynamicTable'

    export default {
        name: "InvoiceTable",
        components: {
            ModalComponent,
            DynamicTable
        },
        mounted() {
            this.$bus.$on('open-payment-modal', (field) => {
                this.$modal.show('payment');
                this.invoice = field;
            });
        },
        data() {
            return {
                invoice: [],
                columns: [
                    {
                        name: 'date',
                        label: this.$translations.date,
                        subType: 'date'
                    },
                    {
                        name: 'amount',
                        label: this.$translations.amount,
                        subType: 'number'
                    }
                ]

            }
        },
        computed: {
            action() {
                return '/admin/invoices/payments/' + this.invoice.id;
            }
        }
    }
</script>
