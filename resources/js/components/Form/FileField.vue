<template>
    <div class="field">
        <div class="file" :class="{'has-name': filename}">
            <label class="file-label">
                <input class="file-input" type="file" :name="field.name" v-on:change='changefile'>
                <span class="file-cta">
                    <span class="file-icon">
                        <font-awesome-icon icon="file-upload"></font-awesome-icon>
                    </span>
                    <span class="file-label" v-text="field.label">
                    </span>
                </span>
                <span class="file-name" v-if="filename" v-text="filename">
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
        mixins: [FieldMixin],
        data(){
            return {
                filename: ''
            }
        },
        methods: {
            changefile(e) {
                this.filename = e.target.files[0].name;
            }
        }
    }
</script>

<style scoped>

</style>
