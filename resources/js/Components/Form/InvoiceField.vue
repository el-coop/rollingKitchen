<template>
	<div class="field box">
		<label class="label" v-text="field.label"></label>
		<div class="columns is-mobile">
			<div class="column" :class="{'is-2' : header !== 'item'}" v-for="header in headers">
				<h6 class="title is-6" v-text="$translations[header]"></h6>
			</div>
			<div class="column is-2"></div>
		</div>
		<invoice-line v-for="(entry, index) in values" :key="`{entry.uniqe_id}_${index}`" :index="index"
					  :name="field.name"
					  v-model="values[index]"
					  :options="field.options"
					  @total="updateTotal(index,$event)"
					  @remove="remove(index)"></invoice-line>
		<div class="columns is-mobile">
			<div class="column" :class="{'is-2' : header !== 'item'}" v-for="header in headers">
				<span v-if="header === 'unitPrice'" v-text="$translations.vat"></span>
				<div v-if="header === 'item'" class="select is-fullwidth">
					<select name="tax" v-model="tax">
						<option v-for="(taxLabel,taxValue) in field.taxOptions" :value="taxValue" v-text="taxLabel">
						</option>
					</select>
				</div>
				<span v-if="header === 'total'" v-text="localNumber(totalSum * tax/100)"></span>
			</div>
			<div class="column is-2"></div>
		</div>
		<div class="columns is-mobile">
			<div class="column" :class="{'is-2' : header !== 'item'}" v-for="header in headers">
				<span v-if="header === 'item'" v-text="$translations.total"></span>
				<span v-if="header === 'total'" v-text="localNumber(totalSum * (1 + tax/100))"></span>
			</div>
			<div class="column is-2"></div>
		</div>
		<p v-if="error" class="help is-danger" v-text="errorText"></p>
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
		mixins: [FieldMixin, DatatableFormatters],

		data() {
			let values;
			if (this.field.value.length) {
				values = this.field.value.map((item) => {
					item.unique_id = Math.random().toString(36).substr(2, 9);
					return item;
				});
			} else {
				values = [{
					unique_id: Math.random().toString(36).substr(2, 9)
				}];
			}

			return {
				values,
				headers: ['quantity', 'unitPrice', 'item', 'total'],
				sum: [0],
				tax: 21
			}
		},

		methods: {
			async remove(index) {
				this.values.splice(index, 1);
				if (this.values.length === 0) {
					await Vue.nextTick();
					this.addValue();
				}
			},
			addValue() {
				this.values.push({
					unique_id: Math.random().toString(36).substr(2, 9)
				});
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