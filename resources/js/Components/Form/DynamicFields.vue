<template>
    <div>
        <template v-for="(field,key) in renderFields">
            <component @update:modelValue="(e) => formChanged(e, field.name)" v-model="data[field.name]"
                       v-if="hide.indexOf(field.name) === -1"
                       :error="field.error || null"
                       :is="`${field.type}-field`"
                       :field="field" :key="key">
            </component>
        </template>
    </div>
</template>

<script>
import TextField from './TextField';
import SelectField from './SelectField';
import TextareaField from './TextareatField';

export default {
    name: "DynamicFields",
    components: {
        TextField,
        TextareaField,
        SelectField,
    },
    props: {
        url: {
            type: String,
            default: ''
        },
        fields: {
            type: Array,
            default() {
                return null;
            }
        },
        hide: {
            type: Array,
            default() {
                return [];
            }
        },
        extraData: {
            type: Object,
            default() {
                return {}
            }
        }
    },
    data() {
        return {
            renderFields: [],
            loading: false,
            data: {}
        }
    },
    async created() {
        if (this.fields) {
            this.renderFields = this.fields;
            for (let field of this.renderFields) {
                this.data[field.name] = field.value;
            }
            if (Object.keys(this.extraData).length != 0) {
                for (let field of this.extraData) {
                    this.data[field.name] = field.value;
                }
            }
            return;
        }

        try {
            this.loading = true;
            const response = await axios.get(this.url);

            this.renderFields = response.data;
            for (let field of this.renderFields) {
                this.data[field.name] = field.value;
            }
        } catch (error) {
            this.$toast.error(this.$translations.tryLater, this.$translations.operationFiled);
        }
        this.loading = false;
    },
    provide() {
        return {
            formValues: this.data
        }
    },
    methods: {
        formChanged(value, name) {
            this.$emit('update:data', value, name);
        },

        updateData(value, name) {
            this.data[name] = value;
        }
    }
}
</script>

<style scoped>
</style>
