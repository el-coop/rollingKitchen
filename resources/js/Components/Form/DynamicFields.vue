<template>
	<div>
		<component v-if="hide.indexOf(field.name) === -1"
				   :error="field.error || null"
				   v-for="(field,key) in fields" :is="`${field.type}-field`"
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
        }
    }
</script>

<style scoped>
</style>
