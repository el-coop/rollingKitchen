<template>
	<form @submit.prevent="handleSubmit" class="mt-1">
		<SelectField @update:modelValue="band = $event" :field="{
		label: $translations.band,
		value: input.id || 1,
		name: '',
		options: bands,
	}"/>
		<SelectField @update:modelValue="stage = $event" :field="{
		label: $translations.stage,
		value: input.stage || 1,
		name: '',
		options: stages,
	}"/>
		<TextField @update:modelValue="payment = $event" :field="{
		required: true,
		label: $translations.payment,
		value: input.payment || null,
		name: 'payment',
		subType: 'number',
		icon: 'euro-sign',
		callbackOptions: {prefix: '€'},
		callback: 'localNumber|prefix'
	}"/>
		<TextField @update:modelValue="endTime = $event" :field="{
		required: true,
		label: $translations.endTime,
		value: input.end_time || null,
		name: 'end_time',
		subType: 'time',
	}"/>
        <slot/>
		<div class="buttons">
			<button class="button is-fullwidth is-success" type="submit">
				Save
			</button>
		</div>
	</form>
</template>

<script>
	import SelectField from "../../Form/SelectField";
    import TextField from "../../Form/TextField";
    export default {
		name: "CalendarModal",
        components: {TextField, SelectField},
        props: {
			input: {
				type: Object,
				required: true
			},

			output: {
				type: Function,
				required: true
			},

			bands: {
				type: Object,
				required: true
			},
			stages: {
				type: Object,
				required: true
			}
		},

		data() {
			return {
				band: this.input.id || 1,
				stage: this.input.stage || 1,
				payment: this.input.payment || '',
				endTime: this.input.end_time || ''
			}
		},

		methods: {
			handleSubmit() {
				this.output({
					band: this.band,
					stage: this.stage,
					payment: this.payment,
					end_time: this.endTime
				});

				this.$emit('submit');
			}
		}
	}
</script>
