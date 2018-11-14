<script>
    export default {
        name: "field-list-page",
        props: {
            form: {
                type: String,
                required: true
            },
            fields: {
                type: Array,
                required: true
            }
        },
        data() {
            return {
                object: null,
                order: [],
            }
        },
        mounted() {
            this.$bus.$on('open-edit-modal', (field) => {
                this.object = field;
                this.$modal.show('fieldForm');
            });
            this.$bus.$on('open-create-modal', () => {
                if (this.object) {
                    this.object = null;
                }
                this.$modal.show('fieldForm');
            });
            this.$bus.$on('get-order', (newOrder) => {
                this.order = [];
                this.order = newOrder;
            });
        },
        created(){
           this.fields.forEach((field) => {
                this.order.push(field.id);
            })
        }
    }
</script>
