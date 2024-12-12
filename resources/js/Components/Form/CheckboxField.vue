<template>
    <div class="field" v-if="Object.keys(field.options).length">
        <label class="label" v-text="field.label" v-if="field.label && !(field.hideLabel || false)"></label>
        <label class="checkbox" v-for="(option, index) in field.options" :key="index">
            <input type="checkbox" :name="fieldName(index)" :value="index" :checked="option.checked === true || value ? true :false"
                   @keypress.enter.prevent>
            <span v-text="option.name" class="checkbox__name"/>&nbsp;
        </label>
        <p v-if="error" class="help is-danger" v-text="errorText"></p>
    </div>
</template>

<script>
import FieldMixin from './FieldMixin';

export default {
    name: "CheckboxField",
    mixins: [FieldMixin],

    methods: {
        fieldName(index) {
            let name = this.field.name;
            if (this.field.options.length > 1 || Object.keys(this.field.options).length > 1) {
                name += `[${index}]`;
            }
            return name;
        }
    }
}
</script>
<style scoped>
.checkbox + .checkbox {
    margin-left: .5em;
}

.checkbox__name {
    margin-left: .3em;
}
</style>
