<script>
    import DynamicTable from '../Utilities/DynamicTable';
    export default {
        extends: DynamicTable,
        name: "InvoicePaymentsTable",
        methods: {
            calculateTotal(){
                let total = 0;
                this.fields.forEach((field) => {
                    total += parseFloat(field.amount);
                });
                return total;
            },
            updateObject(object) {
                if (Object.keys(this.object).length === 0) {
                    this.fields.push(object);
                } else {
                    const editedId = this.fields.findIndex((item) => {
                        return item.id === this.object.id;
                    });
                    this.fields.splice(editedId, 1, object);
                }
                this.closeModal();
                this.$emit('updated-total', this.calculateTotal());
            },
            async destroy(field) {
                this.deleteing = field.id;
                try {
                    await axios.delete(`${this.action}/${field.id}`);
                    this.fields.splice(this.fields.indexOf(field), 1);
                    this.$toast.success(this.$translations.deleteSuccess);
                } catch (error) {
                    this.$toast.error(this.$translations.tryLater, this.$translations.operationFiled);
                }
                this.deleteing = null;
                this.$emit('updated-total', this.calculateTotal());
            },
        }
    }
</script>
<style scoped>
.table thead tr:last-child th {
    border-bottom-width: 2px !important;
}
</style>
