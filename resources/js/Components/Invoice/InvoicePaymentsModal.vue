<template>
    <slot :open="open"/>
    <ModalComponent height="100%" :width="800" :pivotY="0"
                    :open="modalOpen"
                    @close="modalOpen = false"
                    :pivotX="1">
        <InvoicePaymentsTable v-if="invoice.payments" @updated-total="updateInvoice" :columns="columns"
                              :init-fields="invoice.payments"
                              :action="action">
        </InvoicePaymentsTable>
        <div v-else class="has-text-centered">
            <a class="button is-loading"></a>
        </div>
    </ModalComponent>
</template>

<script>
import ModalComponent from '../Utilities/ModalComponent';
import DynamicTable from '../Utilities/DynamicTable'
import InvoicePaymentsTable from './InvoicePaymentsTable';

export default {
    name: "InvoicePaymentsModal",
    emits: ['close'],
    components: {
        ModalComponent,
        DynamicTable,
        InvoicePaymentsTable
    },
    props: {
        fromUrl: {
            type: Boolean,
            default: false
        },
        field: {
            type: Object,
        },
        onUpdate: {
            type: Function,
            required: true
        }

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
            onAdd: {},
            modalOpen: false
        }
    },
    computed: {
        action() {
            return '/admin/invoices/payments/' + this.invoice.id;
        }
    },
    async beforeMount(){
        this.invoice = [];
        if (this.fromUrl) {
            const response = await axios.get('/admin/invoices/payments/' + this.field.id);
            this.invoice = response.data;
        } else {
            this.invoice = this.field;
        }
    },
    methods: {
        updateInvoice(total) {
            this.invoice.totalPaid = total;
            this.invoice.amountLeft = this.invoice.total - total;
            this.onUpdate(this.invoice);
        },
        async open() {
            this.modalOpen = true;
        }
    }
}
</script>
