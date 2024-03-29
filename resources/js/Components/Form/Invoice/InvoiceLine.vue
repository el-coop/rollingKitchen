<template>
	<div class="columns is-mobile">
		<div class="column is-2">
			<input v-model="quantity" required type="number" @keypress.enter.prevent
				   :name="`${name}[${index}][quantity]`" class="input">
		</div>
		<div class="column is-2">
			<input v-model="unitPrice" required type="number" min="0" step="0.01" @keypress.enter.prevent
				   :name="`${name}[${index}][unitPrice]`" class="input">
		</div>
		<div class="column is-2" v-if="individualTax">
			<div class="select is-fullwidth">
				<select :name="`${name}[${index}][tax]`" v-model="tax" @keypress.enter.prevent>
					<option v-for="(taxLabel,taxValue) in taxOptions" :value="taxValue" v-text="taxLabel">
					</option>
				</select>
			</div>
		</div>
		<div class="column">
			<div class="dropdown is-hoverable w-100">
				<div class="dropdown-trigger fill-parent">
					<input v-model="item" required @keypress.enter.prevent
						   :name="`${name}[${index}][item]`" class="input">
				</div>
				<div class="dropdown-menu" v-if="options.length > 0">
					<div class="dropdown-content">
						<a class="dropdown-item" v-for="(option, index) in options" :key="index" v-text="option.item"
						   @click="updateValue(option)"></a>
					</div>
				</div>
			</div>
		</div>
		<div class="column" :class="{'is-1' : individualTax, 'is-2': ! individualTax}" v-text="localNumber(total)">
		</div>
		<div class="column is-2">
			<button class="button is-danger" @click="remove" v-text="$translations.delete"
					type="button"></button>
		</div>
	</div>
</template>

<script>
	import DatatableFormatters from "../../Utilities/Datatable/DatatableFormatters";

	export default {
		name: "InvoiceLine",
        compatConfig: { COMPONENT_V_MODEL: false },
		mixins: [DatatableFormatters],
        emits: ['update:modelValue','total','remove'],
		props: {
			name: {
				type: String,
				required: true
			},
			modelValue: {
				default() {
					return null;
				}
			},
			options: {
				type: Array,
				required: true
			},
			index: {
				type: Number,
				required: true
			},
			individualTax: {
				type: Boolean,
				default: false
			},
			taxOptions: {
				type: Object,
				default() {
					return {}
				}
			}
		},

		data() {
			return {
				quantity: this.modelValue.quantity,
				unitPrice: this.modelValue.unitPrice,
				item: this.modelValue.item,
				totalVal: 0,
				tax: this.modelValue.tax || 0
			}
		},

        beforeUnmount() {
			this.$emit('total', 0);
		},

		methods: {
			updateValue(option) {
				this.unitPrice = option.unitPrice;
				this.item = option.item;
			},

			remove() {
				this.$emit('remove');
			}
		},

		computed: {
			total() {
				let val = 0;
				if (this.modelValue.quantity && this.modelValue.unitPrice) {
					val = (this.modelValue.quantity * this.modelValue.unitPrice) * (1 + this.tax / 100);
					val = val.toFixed(2);
				}
				if (val != this.totalVal) {
					this.$emit('total', val);
					this.totalVal = val;
				}
				return val;

			}
		},

		watch: {
			quantity(value) {
				this.$emit('update:modelValue', {
					quantity: this.quantity,
					unitPrice: this.unitPrice,
					tax: this.tax,
					item: this.item,
				});
			},
			item() {
				this.$emit('update:modelValue', {
					quantity: this.quantity,
					unitPrice: this.unitPrice,
					tax: this.tax,
					item: this.item,
				});
			},
			unitPrice(value) {
				if (value < 0) {
					this.unitPrice = 0;
				}
				this.$emit('update:modelValue', {
					quantity: this.quantity,
					unitPrice: this.unitPrice,
					tax: this.tax,
					item: this.item,
				});
			}

		}

	}
</script>

<style scoped lang="scss">
	.w-100 {
		width: 100%;
	}

	.column {
		min-width: 150px;

		&.is-2, &.is-1 {
			min-width: 75px;
		}
	}
</style>
