<template>
    <div class="field">
        <div class="file">
            <label class="file-label">
                <input class="file-input" type="file" :name="field.name">
                <span class="file-cta">
                    <span class="file-icon">
                        <font-awesome-icon icon="file-upload"></font-awesome-icon>
                    </span>
                    <span class="file-label" v-text="field.label">

                    </span>
                </span>
            </label>
        </div>
        <p v-if="error" class="help is-danger" v-text="errorText"></p>
    </div>
</template>

<script>
    import FieldMixin from './FieldMixin';

    export default {
        name: "FileField",
        mixins: [FieldMixin]
    }
</script>

<style scoped>

</style>
