<template>
    <div class="field">
        <label class="label">{{ field.label }}
            <tooltip v-if="field.hasOwnProperty('tooltip')" :text="field.tooltip"></tooltip>
        </label>
        <div class="control">
			<textarea class="textarea" :class="{'is-danger': error}" v-model="value" :name="field.name"
                      :disabled="field.readonly || conditioned" :placeholder="field.placeholder || ''"></textarea>
        </div>
        <p v-if="error" class="help is-danger" v-text="errorText"></p>
    </div>
</template>

<script>
import FieldMixin from './FieldMixin';

export default {
    name: "TextareaField",
    mixins: [FieldMixin],
    computed: {
        conditioned() {
            return this.field.hasOwnProperty('condition_field') && this.field.condition_value.map((value) => parseInt(value)).includes(this.formValues[this.field.condition_field]);
        }
    },
    inject: ['formValues']
}
</script>
