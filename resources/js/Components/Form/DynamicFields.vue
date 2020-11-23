<template>
	<div>
		<component v-if="hide.indexOf(field.name) === -1"
				   :error="field.error || null"
				   v-for="(field,key) in renderFields" :is="`${field.type}-field`"
				   :field="field" :key="key">
		</component>
	</div>
</template>

<script>
    import TextField from './TextField';
    import SelectField from './SelectField';
    import TextareaField from './TextareatField';
    export default {
        name: "DynamicFields",
        components: {
            TextField,
            TextareaField,
            SelectField,
        },
        props: {
            url: {
                type: String,
                default: ''
            },
            fields: {
                type: Array,
                default() {
                    return null;
                }
            },
            hide: {
                type: Array,
                default() {
                    return [];
                }
            },
        },
		data(){
            return {
                renderFields: [],
                loading: false,
				fieldValues: {}
            }
        },
        async created() {
            if (this.fields) {
                return this.renderFields = this.fields;
            }

            try {
                this.loading = true;
                const response = await axios.get(this.url);

                this.renderFields = response.data;
            } catch (error) {
                this.$toast.error(this.$translations.tryLater, this.$translations.operationFiled);
            }
            this.loading = false;
		},
		methods: {
        	populateFields(payload){
        		let fieldValues = payload;
        		let newFields = [];
        		let len = this.renderFields.length;
        		let i;
        		for (i = 0; i < len; i++) {
        			let field = this.renderFields.shift();
        			field.value = fieldValues[field.name];
					this.renderFields.push(field);
				}
			}
		},
		mounted(){
			this.$bus.$on('use-application', this.populateFields);
		}
	}
</script>

<style scoped>
</style>
