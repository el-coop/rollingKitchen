<template>
    <div class="field">
        <label class="label" v-text="field.label"></label>
        <div class="control">
            <div class="select is-fullwidth" :class="{'is-danger': error}">
                <select v-model="value" :name="field.name" :disabled="field.readonly">
                    <option v-for="(conditionalField, key) in conditionalFields" :key="key" :value="conditionalField"
                            v-text="conditionalField"></option>
                </select>
            </div>
            <p v-if="error" class="help is-danger" v-text="errorText"></p>
        </div>

    </div>
    <div class="field">
        <div class="control">
            <div class="select is-fullwidth" :class="{'is-danger': error}">
                <select v-model="condition" name="condition_value">
                    <option v-for="(condition, key) in conditions[value]" :key="key" :value="key"
                            v-text="condition"></option>
                </select>
            </div>
            <p v-if="error" class="help is-danger" v-text="errorText"></p>
        </div>

    </div>
</template>

<script>
import FieldMixin from './FieldMixin';

export default {
    name: "ConditionalField",
    mixins: [FieldMixin],
    data() {
        let conditionalFields = [''];
        let conditions = {};
        for (const [key, option] of Object.entries(this.field.options)){
            conditionalFields.push(option.name);
            conditions[option.name] = option.options;
        }
        return {
            conditions: conditions,
            conditionalFields: conditionalFields,
            condition: this.field.condition
        }
    }
}
</script>
