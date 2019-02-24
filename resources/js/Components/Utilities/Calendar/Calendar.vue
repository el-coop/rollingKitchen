<template>
	<div>
		<div class="buttons" v-if="currentlyDisplaying-1 < Math.max(this.numberOfDays)">
			<button class="button is-primary" v-html="this.$translations.previous" @click="changeStartDate(-1)"
					type="button"
					:disabled="daysOffset === 0">
			</button>
			<button class="button is-primary" v-html="this.$translations.next" @click="changeStartDate(1)" type="button"
					:disabled="daysOffset === (numberOfDays - currentlyDisplaying + (currentlyDisplaying > 1 ? 1 : 0))">
			</button>
		</div>
		<div class="columns is-mobile">
			<div class="column">
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
					<tr v-for="n in (endHour - startHour)/interval" :key="n">
						<td v-for="i in numberOfDays"
							:key="`${n}_${i}`"
							v-show="calcDate(realStartDate,i - 1) >= currentStart && calcDate(realStartDate,i - 1) < lastDate"
							:style="{ 'min-width': `${columnMinWidth}px`}">
							<calendar-entry :label="formatTime(startHour + (n-1) * interval)" @drop="drop">
								<template #default="{rawData, processedData, edit}">
									<slot name="entry" :rawData="rawData" :processedData="processedData"
										  :edit="edit"
										  :dateTime="`${date(calcDate(realStartDate,i - 1))} ${formatTime(startHour + (n-1) * interval)}`"
										  :init="initData[`${date(calcDate(realStartDate,i - 1))} ${formatTime(startHour + (n-1) * interval)}`] || []"></slot>
								</template>
							</calendar-entry>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			<div class="column" v-if="currentlyDisplaying > 1">
				<div class="box is-inline-block options">
					<h6 class="title is-6" v-text="optionsTitle"></h6>
					<ul>
						<li v-for="(option,index) in options" :key="index" class="mt-1">
							<drag drop-effect="move" :transfer-data="option" v-text="option.name"
								  class="tag is-dark is-medium"></drag>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<modal name="calendar-modal" :adaptive="true" height="auto">
			<div class="modal-body box" v-if="raw">
				<slot name="modal" :input="raw" :output="output"></slot>
			</div>
		</modal>
	</div>
</template>

<script>
	import DatatableFormatters from "../Datatable/DatatableFormatters";
	import VueDragDrop from 'vue-drag-drop';
	import CalendarEntry from './CalendarEntry';

	export default {
		name: "Calendar",

		components: {
			VueDragDrop,
			CalendarEntry
		},

		mixins: [DatatableFormatters],

		props: {
			initData: {},
			startDate: {
				type: String,
				required: true
			},
			numberOfDays: {
				type: Number,
				default: 9
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
			},
			options: {
				type: Array,
				default() {
					return [];
				}
			},
			optionsTitle: {
				type: String,
				default: ''
			},
			resizeable: {
				type: Boolean,
				default: false,
			}
		},

		mounted() {
			this.setWidth();
			if (this.resizeable) {
				window.addEventListener('resize', this.setWidth)
			}
		},

		beforeDestroy: function () {
			if (this.resizeable) {
				window.removeEventListener('resize', this.setWidth)
			}
		},

		data() {
			return {
				realStartDate: new Date(this.startDate),
				daysOffset: 0,
				currentlyDisplaying: Math.min(this.maxParallel, this.numberOfDays),
				raw: null,
				output: null,
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
				let maxDisplay = Math.min(this.maxParallel, this.numberOfDays) + 1;
				while (totalWidth / (this.columnMinWidth * maxDisplay) < 1) {
					maxDisplay--;
				}
				this.currentlyDisplaying = maxDisplay;
			},

			changeStartDate(days) {
				this.daysOffset += days;
			},

			drop(payload) {
				this.raw = payload.raw;
				this.output = payload.output;
				this.$modal.show('calendar-modal');
			},
			formatTime(time) {
				let hours = Math.floor(time);
				let minutes = time - hours;
				while (hours > 24) {
					hours -= 24;
				}
				minutes = Math.floor(minutes * 60);
				if (hours < 10) {
					hours = '0' + hours;
				}

				if (minutes < 10) {
					minutes = '0' + minutes;
				}
				return `${hours}:${minutes}`;
			}
		},

		computed: {
			lastDate() {
				let currentlyDisplaying = this.currentlyDisplaying;
				if (currentlyDisplaying > 1) {
					currentlyDisplaying -= 1;
				}
				return this.calcDate(this.currentStart, currentlyDisplaying);
			},

			currentStart() {
				return this.calcDate(this.realStartDate, this.daysOffset);
			}
		}
	}
</script>

<style lang="scss" scoped>
	@import "../../../../sass/variables";

	.modal-body {

		.modal-close {
			position: relative;
			float: right;
			top: 0;
			right: 0;

			&:after, &:before {
				background: $contrast-bg;
			}
		}
	}

	.tag {
		cursor: pointer;
	}

	.options {
		position: sticky;
		top: 0;
	}
</style>
