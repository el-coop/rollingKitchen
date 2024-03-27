<template>
    <div class="field">
        <label class="label">{{ field.label }}
            <tooltip v-if="field.hasOwnProperty('tooltip')" :text="field.tooltip"></tooltip>
        </label>
        <div class="control" :class="{'has-icons-left': field.icon || false}">
            <input class="input" :class="{'is-danger': error}" :type="field.subType || 'text'" v-model="value"
                   @keypress.enter.prevent
                   :required="field.required || false"
                   :name="field.name" :step="field.step || 'any'" :disabled="field.readonly || conditioned"
                   :placeholder="field.placeholder || ''">
            <span class="icon is-small is-left" v-if="field.icon || false">
			<font-awesome-icon :icon="field.icon" size="sm"></font-awesome-icon>
			</span>
        </div>
        <p v-if="error" class="help is-danger" v-text="errorText"></p>
    </div>
</template>

<script>
import FieldMixin from './FieldMixin';

export default {
    name: "TextField",
    setup(){
    },
    data(){
        return {
            conditionValue: ''
        }
    },
    mixins: [FieldMixin],
    computed: {
        conditioned() {
            return this.field.hasOwnProperty('condition_field') && this.field.condition_value.includes(this.formValues[this.field.condition_field]);
        }
    },
    inject: ['formValues']
}
</script>
