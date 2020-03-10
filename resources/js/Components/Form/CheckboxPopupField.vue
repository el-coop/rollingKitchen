<template>
    <div>
        <div class="field">
            <label class="label" v-text="field.label" v-if="field.label && !(field.hideLabel || false)"></label>
            <label class="checkbox">
                <input type="checkbox" :name="field.name" :checked="field.checked" :disabled="field.disabled">
                {{field.popup.label}} <a href="#" @click="$modal.show('popup')" v-text="field.popup.a"></a>&nbsp
            </label>
        </div>
        <modal-component name="popup">
            <div v-text="field.popup.text"></div>
        </modal-component>
        <p v-if="error" class="help is-danger" v-text="errorText"></p>
    </div>
</template>

<script>
    import FieldMixin from './FieldMixin';

    export default {
        name: "CheckboxPopupField",
        mixins: [FieldMixin],
    }
</script>
