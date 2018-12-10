<template>
    <div class="field">
        <label class="label" v-text="field.label"></label>
        <div class="control">
            <vue-json-pretty  :data="JSON.parse(value)" :options="{maxDepth: 100}" ></vue-json-pretty>
        </div>
    </div>
</template>

<script>
    import FieldMixin from './FieldMixin';
    import VueJsonPretty from 'vue-json-pretty'

    export default {
        components: {
            VueJsonPretty
        },
        name: "JsonField",
        mixins: [FieldMixin]
    }
</script>

<style scoped>

</style>
