<template>
	<div class="field">
		<div class="box">
			<h5 class="title is-5" v-text="field.label"></h5>
			<div class="columns is-mobile">
				<div class="column"
					 :class="headerClass(header)" v-for="header in headers">
					<h6 class="title is-6" v-text="$translations[header]"></h6>
				</div>
				<div class="column is-2"></div>
			</div>
			<invoice-line v-for="(entry, index) in values" :key="`{entry.uniqe_id}_${index}`" :index="index"
						  :name="field.name"
						  v-model="values[index]"
						  :individual-tax="field.individualTax || false"
						  :tax-options="field.taxOptions"
						  :options="field.options"
						  @total="updateTotal(index,$event)"
						  @remove="remove(index)"></invoice-line>
			<div class="columns is-mobile" v-if="!(field.individualTax || false)">
				<div class="column" :class="{'is-2' : header !== 'item'}" v-for="header in headers">
					<span v-if="header === 'unitPrice'"
						  v-text="$translations.vat"></span>
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
				<div class="column" :class="headerClass(header)" v-for="header in headers">
					<span v-if="header === 'item'" v-text="$translations.total"></span>
					<span v-if="header === 'total'" v-text="localNumber(totalSum * (1 + tax/100))"></span>
				</div>
				<div class="column is-2"></div>
			</div>
			<p v-if="error" class="help is-danger" v-text="errorText"></p>
			<div class="button is-info" v-text="$translations.add" @click="addValue" type="button"></div>
		</div>
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

			const headers = ['quantity', 'unitPrice', 'item', 'total'];
			let tax = 21;

			if (this.field.individualTax || false) {
				headers.splice(-2, 0, 'vat');
				tax = 0;
			}

			return {
				values,
				headers: headers,
				sum: [0],
				tax
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
			},

			headerClass(header) {
				if (header === 'item') {
					return {};
				}

				if (header !== 'total' || !(this.field.individualTax || false)) {
					return 'is-2';
				}

				return 'is-1';
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

<style scoped lang="scss">

	.box {
		overflow: auto;

		.column {
			min-width: 150px;

			&.is-2, &.is-1 {
				min-width: 75px;
			}
		}
	}
</style>