<template>
    <div>
        <div class="mb-5" :class="{'is-flex is-align-items-center': !field.stacked}">
            <p :class="field.stacked ? 'label' : 'title is-4 mb-0 mr-5'" v-if="field.label" v-text="field.label"></p>
            <label class="radio mr-4">
                <input type="radio" :name="field.name" value="1" v-model="value" :disabled="field.disabled">
                {{ field.yes }}
            </label>
            <label class="radio mr-4">
                <input type="radio" :name="field.name" value="0" v-model="value" :disabled="field.disabled">
                {{ field.no }}
            </label>
            <tooltip v-if="field.tooltip" :text="field.tooltip"></tooltip>
        </div>
        <p v-if="error" class="help is-danger mb-4" v-text="errorText"/>
        <slot v-if="value === '1' || field.alwaysShowContent"></slot>
    </div>
</template>

<script>
import FieldMixin from "./FieldMixin";

export default {
    name: "YesNoTooltipField",
    mixins: [FieldMixin],
    data() {
        return {
            value: this.field.value == null ? null : String(Number(this.field.value))
        }
    }
}
</script>
