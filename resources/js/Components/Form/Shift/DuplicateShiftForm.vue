<template>
    <ajax-form method='post' :action="action" @submitting="loading = true" @submitted="submitted">
        <button type="submit" class="button is-info" :class="{'is-loading': loading}" :disabled="loading">
            {{ $translations.duplicate }}
        </button>
    </ajax-form>
</template>
<script>
import AjaxForm from '../AjaxForm';
export default {
    name: "DuplicateShiftForm",
    components: { AjaxForm },
    props: {
        shiftId: {
            type: Number,
            required: true
        }
    },
    data() {
        return { loading: false }
    },
    computed: {
        action() {
            return window.location.pathname + '/duplicate/' + this.shiftId;
        }
    },
    methods: {
        submitted(response) {
            this.loading = false;
            if (response.status === 200 || response.status === 201) {
                this.$emit('success', response.data);
            }
        }
    }
}
</script>
