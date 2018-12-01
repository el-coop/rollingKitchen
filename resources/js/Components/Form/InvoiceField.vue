<template>
	<div class="field box">
		<label class="label" v-text="field.label"></label>
		<div class="columns is-mobile">
			<div class="column" :class="{'is-2' : header !== 'item'}" v-for="header in headers">
				<h6 class="title is-6" v-text="$translations[header]"></h6>
			</div>
			<div class="column is-2"></div>
		</div>
		<invoice-line v-for="(entry, index) in values" :key="`${entry.item}_${entry.quantity}_${index}`" :index="index" :name="field.name"
					  v-model="values[index]"
					  :options="field.options"
					  @total="updateTotal(index,$event)"
					  @remove="remove(index)"></invoice-line>
		<div class="columns is-mobile">
			<div class="column" :class="{'is-2' : header !== 'item'}" v-for="header in headers">
				<span v-if="header === 'item'">BTW 21%</span>
				<span v-if="header === 'total'" v-text="localNumber(totalSum * 0.21)"></span>
			</div>
			<div class="column is-2"></div>
		</div>
		<div class="columns is-mobile">
			<div class="column" :class="{'is-2' : header !== 'item'}" v-for="header in headers">
				<span v-if="header === 'item'" v-text="$translations.total"></span>
				<span v-if="header === 'total'" v-text="localNumber(totalSum * 1.21)"></span>
			</div>
			<div class="column is-2"></div>
		</div>


		<div class="button is-info" v-text="$translations.add" @click="addValue" type="button"></div>
	</div>
</template>

<script>
	import FieldMixin from './FieldMixin';
	import InvoiceLine from "./Invoice/InvoiceLine";
	import DatatableFormatters from "../Utilities/Datatable/DatatableFormatters";

	export default {
		name: "InvoiceField",
		components: {InvoiceLine},
		mixins: [FieldMixin,DatatableFormatters],

		data() {
			return {
				values: this.field.value,
				headers: ['quantity', 'unitPrice', 'item', 'total'],
				sum: [0]
			}
		},

		methods: {
			remove(index) {
				this.values.splice(index, 1);
			},
			addValue() {
				this.values.push({});
			},
			updateTotal(index, payload) {
				this.sum.splice(index, 1, payload);
			}
		},

		computed: {
			totalSum() {
				return this.sum.reduce((total, num) => {
					return total + num;
				});
			}
		}
	}
</script>