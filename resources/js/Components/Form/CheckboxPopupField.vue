<template>
    <div>
        <div class="field">
            <label class="label" v-text="field.label" v-if="field.label && !(field.hideLabel || false)"></label>
            <label class="checkbox">
                <input type="checkbox" :name="field.name" :checked="field.checked" :disabled="field.disabled">
                {{ field.popup.label }} <a href="#" @click="open=true" v-text="field.popup.a"></a>&nbsp
            </label>
        </div>
        <ModalComponent name="popup" :open="open" @close="open=false">
            <div v-text="field.popup.text"/>
        </ModalComponent>
        <p v-if="error" class="help is-danger" v-text="errorText"/>
    </div>
</template>

<script>
import FieldMixin from './FieldMixin';

export default {
    name: "CheckboxPopupField",
    mixins: [FieldMixin],
    data() {
        return {
            open: false
        }
    }
}
</script>
