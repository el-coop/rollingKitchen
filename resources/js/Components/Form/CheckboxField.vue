<template>
    <div class="field" v-if="Object.keys(field.options).length">
        <label class="label" v-text="field.label" v-if="field.label && !(field.hideLabel || false)"></label>
        <label class="checkbox" v-for="(option, index) in field.options" :key="index">
            <input type="checkbox" :name="`${field.name}[]`" :value="index" :checked="option.checked || value"
                   @keypress.enter.prevent>
            <span v-text="option.name"></span>&nbsp;&nbsp;
        </label>
    </div>
</template>

<script>
    import FieldMixin from './FieldMixin';
    export default {
        name: "CheckboxField",
        mixins: [FieldMixin]
    }
</script>
<style scoped>
    .checkbox + .checkbox {
        margin-left: .5em;
    }
</style>
