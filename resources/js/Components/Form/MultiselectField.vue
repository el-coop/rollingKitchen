<template>
    <div class="field">
        <label class="label" v-text="field.label"></label>
        <div class="control">
            <input type="hidden" v-for="(option, index) in value" :name="`${field.name}[${index}]`" :value="option.id">
            <VueMultiselect v-model="value" :options="field.options" :multiple="true" :label="field.optionsLabel"
                         :hide-selected="true"
                         track-by="id"/>
            <p v-if="error" class="help is-danger" v-text="errorText"></p>
        </div>
    </div>
</template>

<script>
import VueMultiselect from 'vue-multiselect'
import FieldMixin from './FieldMixin';

export default {
    components: {
        VueMultiselect
    },
    name: "MultiselectField",
    mixins: [FieldMixin],

    data() {
        return {
            value: this.field.value
        }
    }
}
</script>

<style lang="scss">
@import "~vue-multiselect/dist/vue-multiselect.css";
</style>
