<script>
export default {
    emits: ['update:modelValue'],
    props: {
        field: {
            required: true,
            type: Object
        },
        error: {
            default: null,
            type: Array
        }
    },
    data() {
        return {
            value: this.field.value
        }
    },

    computed: {
        errorText() {
            if (!this.error) {
                return '';
            }
            return this.error[0].replace(this.field.name.replace('[', '.').replace(']', '').replace('_', ' '), this.field.label);
        }
    },

    watch: {
        value: {
            handler(value) {
                this.$emit('update:modelValue', value);
            },
            deep: true
        }
    }
}
</script>

