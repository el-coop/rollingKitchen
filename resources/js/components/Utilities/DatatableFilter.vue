<template>
	<div>
		<h5 class="title is-5">Filters:</h5>
		<div v-for="field in fields" class="field">
			<label class="label" v-text="field.title || field.name"></label>
			<div class="control">
				<div v-if="Array.isArray(field.filter)" class="select is-fullwidth">
					<select v-model="filters[field.name]">
						<option></option>
						<option v-for="option in field.filter" :valu="option" v-text="option"></option>
					</select>
				</div>
				<input v-else v-model="filters[field.name]" class="input" type="text" placeholder="">
			</div>
		</div>
		<div class="buttons">
			<button class="button is-primary" @click="filter">Filter</button>
			<button class="button" @click="filters={}; filter()">Clear</button>
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
			}
		},

		data() {
			return {
				fields: this.tableFields.filter((field) => {
					return typeof field.filter === "undefined" || field.filter
				}),
				filters: {}
			};
		},

		methods: {
			filter() {
				this.$emit('filter', this.filters);
			}
		}
	}
</script>

<style scoped>

</style>