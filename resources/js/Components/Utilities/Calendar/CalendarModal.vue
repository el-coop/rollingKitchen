<template>
	<form @submit.prevent="handleSubmit" class="mt-1">
		<select-field @input="band = $event" :field="{
		label: $translations.band,
		value: input.id || 1,
		name: '',
		options: bands,
	}"></select-field>
		<select-field @input="stage = $event" :field="{
		label: $translations.stage,
		value: input.stage || 1,
		name: '',
		options: stages,
	}"></select-field>
		<text-field @input="payment = $event" :field="{
		required: true,
		label: $translations.payment,
		value: input.payment || null,
		name: 'payment',
		subType: 'number',
		icon: 'euro-sign',
		callbackOptions: {prefix: '€'},
		callback: 'localNumber|prefix'
	}"></text-field>
		<text-field @input="endTime = $event" :field="{
		required: true,
		label: $translations.endTime,
		value: input.end_time || null,
		name: 'end_time',
		subType: 'time',
	}"></text-field>
		<div class="buttons">
			<button class="button is-fullwidth is-success" type="submit">
				Save
			</button>
		</div>
	</form>
</template>

<script>
	export default {
		name: "CalendarModal",
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
				this.$modal.hide('calendar-modal');
			}
		}
	}
</script>
