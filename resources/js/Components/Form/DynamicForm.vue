<template>
    <AjaxForm :headers="headers" @errors="handleErrors" :method="method" :action="url" @submitting="submitting = true"
              @submitted="submitted" :extraData="extraData" @alternative-submitting="alternativeSubmitting=true">
        <div v-if="loading" class="has-text-centered">
            <a class="button is-loading"></a>
        </div>
        <template v-for="(field,key) in fields">
            <component v-model="data[field.name]" v-if="(hide.indexOf(field.name) === -1)"
                       :error="errors[field.name.replace('[','.').replace(']','')] || null"
                       :is="`${field.type}-field`"
                       :field="field" :loading="alternativeSubmitting" :key="key">
            </component>
        </template>
        <slot :fields="fields">
        </slot>
        <div class="buttons">
            <button v-if="!loading" class="button is-fullwidth" :class="[submitting ? 'is-loading' : '', buttonClass]"
                    type="submit" v-text="buttonText">
            </button>
        </div>
    </AjaxForm>
</template>

<script>
import TextField from './TextField';
import SelectField from './SelectField';
import TextareaField from './TextareatField';

export default {
    name: "DynamicForm",
    components: {
        TextField,
        TextareaField,
        SelectField,
    },

    props: {
        buttonText: {
            type: String,
            default() {
                return $translations.save;
            }

        },
        successToast: {
            type: String,
            default() {
                return $translations.updateSuccess;
            }

        },

        url: {
            type: String,
            default: ''
        },
        initFields: {
            type: Array,
            default() {
                return null;
            }
        },
        method: {
            type: String,
            default: 'patch'
        },
        onDataUpdate: {
            type: Function,
        },

        hide: {
            type: Array,
            default() {
                return [];
            }
        },
        buttonClass: {
            type: String,
            default: 'is-success'
        },
        extraData: {
            type: Object,
            default() {
                return {};
            }
        },
        headers: {
            type: Object,
            default() {
                return {
                    'Content-Type': 'application/json'
                };
            }
        }
    },

    data() {
        return {
            fields: [],
            loading: false,
            submitting: false,
            alternativeSubmitting: false,
            errors: {},
            data: {},
        };
    },

    async created() {
        if (this.initFields) {
            this.fields = this.initFields;
            for (let field of this.fields) {
                this.data[field.name] = field.value;
            }
            return;
        }

        try {
            this.loading = true;
            const response = await axios.get(this.url);
            this.fields = response.data;
            for (let field of this.fields) {
                this.data[field.name] = field.value;
            }
        } catch (error) {
            this.$toast.error(this.$translations.tryLater, this.$translations.operationFiled);
        }
        this.loading = false;
    },

    methods: {
        handleErrors(errors) {
            this.errors = errors;
            if (Object.keys(this.errors).length) {
                this.$toast.error(this.$translations.pleaseCorrect, this.$translations.formErrors);
            }
        },

        submitted(response) {
            this.submitting = false;
            this.alternativeSubmitting = false;
            if (response.status === 200 || response.status === 201) {
                if (response.headers['content-disposition'] && response.headers['content-disposition'].indexOf('attachment') === 0) {
                    const filename = response.headers['content-disposition'].split('filename="').pop().split('"')[0];
                    this.download(response.data, filename, response.headers['content-type']);
                    return;
                }
                this.$toast.success(this.successToast);
                if (this.onDataUpdate) {
                    this.onDataUpdate(response.data);
                } else {
                    this.$emit('object-update', response.data);
                }
            }
        },

        download(data, filename, mime) {
            const blob = new Blob([data], {type: mime || 'application/pdf'});
            const blobURL = window.URL.createObjectURL(blob);
            const tempLink = document.createElement('a');
            tempLink.style.display = 'none';
            tempLink.href = blobURL;
            tempLink.setAttribute('download', filename);

            // Safari thinks _blank anchor are pop ups. We only want to set _blank
            // target if the browser does not support the HTML5 download attribute.
            // This allows you to download files in desktop safari if pop up blocking
            // is enabled.
            if (typeof tempLink.download === 'undefined') {
                tempLink.setAttribute('target', '_blank');
            }

            document.body.appendChild(tempLink);
            tempLink.click();
            document.body.removeChild(tempLink);
            window.URL.revokeObjectURL(blobURL);

        },

    },
    provide() {
        return {
            formValues: this.data
        }
    }
}
</script>
