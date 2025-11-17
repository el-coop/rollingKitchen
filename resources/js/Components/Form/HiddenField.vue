<template>
    <div class="field">
        <input v-model="value" type="hidden" :name="field.name">
    </div>
</template>

<script>
import FieldMixin from './FieldMixin';

export default {
    name: "HiddenField",
    mixins: [FieldMixin],
    inject: ['formValues'],
}
</script>
