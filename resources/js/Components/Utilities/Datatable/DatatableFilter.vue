<template>
	<div v-if="fields.length > 0">
		<h5 class="title is-5" v-text="`${filtersText}:`"></h5>
		<div v-for="field in fields" class="field">
			<label class="label" v-text="field.title || field.name"></label>
			<div class="control">
				<div v-if="typeof field.filter === 'object'" class="select is-fullwidth">
					<select v-model="filters[field.name]">
						<option></option>
						<option v-for="(option, key) in field.filter" :value="key" v-text="option"></option>
					</select>
				</div>
				<input v-else v-model="filters[field.name]" class="input" type="text" placeholder="">
			</div>
		</div>
		<div class="buttons">
			<button class="button is-primary" @click="filter" v-text="filterText"></button>
			<button class="button" @click="filters={}; filter()" v-text="clearText"></button>
		</div>
	</div>
</template>

<script>
	export default {
		name: "DatatableFilter",
		props: {
			tableFields: {
				type: Array,
				required: true
			},
			filtersText: {
				type: String,
				default: 'Filters'
			},
			filterText: {
				type: String,
				default: 'Filter'
			},
			clearText: {
				type: String,
				default: 'Clear'
			},
			initFilters: {
				type: Object,
				default() {

				}
			}
		},

		data() {
			return {
				fields: this.tableFields.filter((field) => {
					return (typeof field.filter === "undefined" || field.filter) && (typeof field.visible === "undefined" || field.visible);
				}),
				filters: this.initFilters
			};
		},

		methods: {
			filter() {
				this.$emit('filter', this.filters);
				history.replaceState({}, "", `${window.location.origin}${window.location.pathname}?filters=` + JSON.stringify(this.filters));
			}
		}
	}
</script>
