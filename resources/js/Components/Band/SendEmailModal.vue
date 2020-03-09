<template>
    <modal-component name="sendEmailModal">
        <div class="card">
            <div class="field">
                <div class="control has-icons-left">
                    <span class="icon is-left">
                        <font-awesome-icon icon="search"></font-awesome-icon>
                    </span>
                    <input class="input" v-model="search">
                </div>
            </div>
            <ajax-form :action="action">
                <checkbox-field v-for="band in filteredBands" :field="band">
                </checkbox-field>
                <div class="buttons">
                    <button class="button is-success is-fullwidth">Send Email</button>
                </div>
            </ajax-form>
        </div>
    </modal-component>
</template>

<script>
    import AjaxForm from "../Form/AjaxForm";
    import CheckboxField from "../Form/CheckboxField";

    export default {
        name: "SendEmailModal",
        components: {
            AjaxForm,
            CheckboxField
        },
        props: {
            bands: {
                type: Object,
                required: true
            },
            action: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                search: "",
                renderedBands: []
            }
        },
        async created() {
            let bands = Object.entries(this.bands);
            bands.forEach((band) => {
                let bandId = band[0];
                let bandName = band[1];
                let bandField = {
                    'name': 'bands[' + bandId + ']',
                    'label': '',
                    'bandName': bandName,
                    'options': {}
                };
                bandField.options[bandId] = {'name': bandName};
                this.renderedBands.push(bandField);
            });
        },
        computed: {
            filteredBands() {
                return this.renderedBands.filter((band) => {
                    return band.bandName.toLowerCase().includes(this.search.toLowerCase());
                })
            }
        }
    }
</script>
