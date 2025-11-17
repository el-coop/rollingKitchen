<template>
    <div>
        <div class="field">
            <label class="label" v-text="field.label"></label>
            <div class="control">
                <div class="select is-fullwidth" :class="{'is-danger': error}">
                    <select v-model="value" :name="field.name" :disabled="field.readonly">
                        <option :class="field.hasOwnProperty('class') ? field.class[field.optionClass[val]] : ''"
                                v-for="(option, val) in field.options" :value="val" v-text="option"></option>
                    </select>
                </div>
                <p v-if="error" class="help is-danger" v-text="errorText"></p>
            </div>
        </div>
        <div v-if="value === 2 || value === 3">
            <ConditionField :field="conditionField">
            </ConditionField>
        </div>
    </div>
</template>

<script>
import FieldMixin from './FieldMixin';
import TextField from "./TextField.vue";
import ConditionField from "./Service/ConditionField.vue";

export default {
    name: "ServiceTypeField",
    components: {ConditionField, TextField},
    mixins: [FieldMixin],
    computed: {
        conditionField() {
            return {
                name: 'scale',
                type: 'condition',
                label: 'scale',
                value: this.field.subValue === null ? [] : this.field.subValue,
                serviceType: this.value
            }
        }
    }
}
</script>
