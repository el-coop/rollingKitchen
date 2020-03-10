<template>
    <div>
        <h4 class="title is-4" v-text="$translations.setlistUpload"></h4>
        <h6 class="subtitle is-6" v-if="setlist.file || false">
            <a :href="url" v-text="$translations.download"></a>
        </h6>
        <dynamic-form :init-fields="[{
		    name: 'owned',
		    type: 'select',
		    label: $translations.ownNumbers,
		    options: {
		        yes: $translations.yes,
		        no: $translations.no,
		        both: $translations.both
		    },
		    value: setlist.owned || '',
		},{
		    name: 'protected',
		    type: 'select',
		    label: $translations.areTheyProtected,
		    options: {
		        yes: $translations.yes,
		        no: $translations.no,
		        both: $translations.both
		    },
		    value: setlist.protected || '',
		},{
			name: 'file',
			type: 'file',
			label: $translations.chooseFile,
		}]" method="post" :headers="{'Content-Type': 'multipart/form-data'}" :button-text="$translations.upload"
                      :url="url" @object-update="handleSubmit">
        </dynamic-form>
    </div>
</template>

<script>
    export default {
        name: "BandSetlistfForm",

        props: {
            initSetlist: {
                type: Object,
                required: true
            },

            bandId: {
                type: String,
                required: true
            }
        },

        data() {
            return {
                setlist: this.initSetlist
            }
        },

        methods: {
            handleSubmit() {
                this.setlist = true;
            }
        },

        computed: {
            url() {
                return `/band/${this.bandId}/setlist`;
            }
        }
    }
</script>
