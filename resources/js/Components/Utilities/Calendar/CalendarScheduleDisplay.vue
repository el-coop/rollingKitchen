<template>
	<div class="tags">
		<span v-for="(entry, index) in entries" class="is-hidden">
			<input :key="`input_band_${index}`" type="hidden"
				   :value="entry.band"
				   :name="`${dateTime}[${index}][band]`">
			<input :key="`input_stage_${index}`" type="hidden"
				   :value="entry.stage"
				   :name="`${dateTime}[${index}][stage]`">
			<input :key="`input_payment_${index}`" type="hidden"
				   :value="entry.payment"
				   :name="`${dateTime}[${index}][payment]`">
		</span>
		<div class="tags has-addons" v-for="entry in entries" :key="entry.stage"
			 @click.stop="openModal({id: entry.band,stage: entry.stage,payment: entry.payment})">
			<span class="tag is-primary" v-text="stages[entry.stage]"></span>
			<span class="tag is-dark" v-text="bands[entry.band]"></span>
			<a class="tag is-delete" @click.stop="remove(entry.stage)"></a>
		</div>
	</div>
</template>

<script>
	export default {
		name: "CalendarScheduleDisplay",
		props: {
			data: {
				type: Object,
				required: true
			},
			bands: {
				type: Object,
				required: true
			},
			stages: {
				type: Object,
				required: true
			},
			edit: {
				type: Function,
				required: false
			},
			init: {
				type: Array,
				default() {
					return [];
				}
			},
			dateTime: {
				type: String,
				required: true
			}
		},

		data() {
			return {
				entries: this.init,
			}
		},

		methods: {
			remove(id, key = 'stage') {
				const index = this.entries.findIndex((entry) => {
					return entry[key] === id;
				});
				if (index < 0) {
					return;
				}
				this.entries.splice(index, 1);
			},
			openModal(payload) {
				this.edit(payload);
			}
		},

		watch: {
			data(value) {
				if (!value.band) {
					return;
				}
				this.remove(value.stage);
				this.remove(value.band, 'band');
				this.entries.push(value);
			}
		}
	}
</script>

<style scoped lang="scss">
	.tags:not(:last-child) {
		margin-bottom: 0;
	}

	.tag {
		cursor: pointer;
	}
</style>
