<template>
	<div>
		<div class="buttons" v-if="currentlyDisplaying < Math.min(this.maxParallel, this.numberOfDays)">
			<button class="button is-primary" v-html="this.$translations.previous" @click="changeStartDate(-1)"
					:disabled="daysOffset === 0">
			</button>
			<button class="button is-primary" v-html="this.$translations.next" @click="changeStartDate(1)"
					:disabled="daysOffset === (numberOfDays - currentlyDisplaying)">
			</button>
		</div>
		<table class="table is-bordered">
			<thead>
			<tr>
				<th class="has-text-centered" v-for="i in numberOfDays"
					v-text="date(calcDate(realStartDate,i - 1))"
					v-show="calcDate(realStartDate,i - 1) >= currentStart && calcDate(realStartDate,i - 1) < lastDate"
					:key="`header_${i}`"
					:style="{ 'min-width': `${columnMinWidth}px`}">
				</th>
			</tr>
			</thead>
			<tbody>
			<tr v-for="n in (endHour - startHour)/interval">
				<td class="has-text-centered" v-for="i in numberOfDays"
					v-text="startHour + (n-1) * interval"
					v-show="calcDate(realStartDate,i - 1) >= currentStart && calcDate(realStartDate,i - 1) < lastDate"
					:style="{ 'min-width': `${columnMinWidth}px`}">
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
	import DatatableFormatters from "../Datatable/DatatableFormatters";

	export default {
		name: "Calendar",

		mixins: [DatatableFormatters],

		props: {
			startDate: {
				type: String,
				required: true
			},
			numberOfDays: {
				type: Number,
				default: 10
			},
			startHour: {
				type: Number,
				default: 0
			},
			endHour: {
				type: Number,
				default: 24
			},
			interval: {
				type: Number,
				default: 0.5
			},
			columnMinWidth: {
				type: Number,
				default: 200
			},
			maxParallel: {
				type: Number,
				default: 7
			}
		},

		mounted() {
			this.setWidth();
			//	window.addEventListener('resize', this.setWidth)
		},

		beforeDestroy: function () {
			//	window.removeEventListener('resize', this.setWidth)
		},

		data() {
			return {
				realStartDate: new Date(this.startDate),
				daysOffset: 0,
				currentlyDisplaying: Math.min(this.maxParallel, this.numberOfDays)
			};
		},

		methods: {
			calcDate(date, days) {
				const newDate = new Date(date);
				newDate.setDate(date.getDate() + days);
				return newDate;
			},

			setWidth() {
				const totalWidth = this.$el.getBoundingClientRect().width;
				let maxDisplay = Math.min(this.maxParallel, this.numberOfDays);
				while (totalWidth / (this.columnMinWidth * maxDisplay) < 1) {
					maxDisplay--;
				}
				this.currentlyDisplaying = maxDisplay;
			},

			changeStartDate(days) {
				this.daysOffset += days;
			}
		},

		computed: {
			lastDate() {
				return this.calcDate(this.currentStart, this.currentlyDisplaying);
			},

			currentStart() {
				return this.calcDate(this.realStartDate, this.daysOffset);
			}
		}
	}
</script>
