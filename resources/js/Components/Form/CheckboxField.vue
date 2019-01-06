<template>
    <div class="field">
        <label class="checkbox">
            <input type="checkbox" v-model="value"" :name="field.name">
            {{field.label}}
        </label>
        <p v-if="error" class="help is-danger" v-text="errorText"></p>
    </div>
</template>
<script>
    import FieldMixin from './FieldMixin';

    export default {
        name: "CheckboxField",
        mixins: [FieldMixin]
    }
</script>
