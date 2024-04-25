<template>
    <div class="mb-3">
        <div class="field">
            <label class="label" v-text="field.label[0]"></label>
            <div class="control">
                <div class="select is-fullwidth" :class="{'is-danger': error}">
                    <select v-model="value" :name="field.name" :disabled="field.readonly">
                        <option v-for="(conditionalField, key) in conditionalFields" :key="key"
                                :value="conditionalField"
                                v-text="conditionalField"></option>
                    </select>
                </div>
                <p v-if="error" class="help is-danger" v-text="errorText"></p>
            </div>
        </div>
        <div class="field">
            <label class="label" v-text="field.label[1]"></label>
            <div class="control">
                <div class="select is-fullwidth" :class="{'is-danger': error}">
                    <input type="hidden" v-for="(option, index) in condition" :name="`condition_value[${index}]`"
                           :value="getValue(value, option)">
                    <vue-multiselect :hide-selected="true" :options="conditionOptions(value)"
                                     :multiple="true" v-model="condition">
                    </vue-multiselect>
                </div>
                <p v-if="error" class="help is-danger" v-text="errorText"></p>
            </div>
        </div>
    </div>
</template>

<script>
import FieldMixin from './FieldMixin';
import VueMultiselect from 'vue-multiselect'

export default {
    name: "ConditionalField",
    components: {
        VueMultiselect
    },
    mixins: [FieldMixin],
    data() {
        let conditionalFields = [''];
        let conditions = {};
        for (const [key, option] of Object.entries(this.field.options)) {
            conditionalFields.push(option.name);
            conditions[option.name] = option.options;
        }
        let condition = [];
        if (Array.isArray(this.field.condition) && this.field.condition.length > 0) {
            let options = Object.values(this.field.options).filter((option) => option.name === this.field.value)[0].options;
            this.field.condition.map((value) => parseInt(value)).forEach((id) => {
                condition.push(options[id])
            })
        }
        return {
            conditions: conditions,
            conditionalFields: conditionalFields,
            condition: condition
        }
    },
    methods: {
        conditionOptions(value) {
            if (typeof this.conditions[value] === 'undefined') {
                return [];
            }
            console.log(value);
            console.log(this.conditions[value]);
            return this.conditions[value];
        },
        getValue(value, option) {
             return this.conditions[value].indexOf(option);
        }

    }
}
</script>
