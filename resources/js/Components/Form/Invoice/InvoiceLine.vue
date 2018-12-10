<template>
	<div class="columns is-mobile">
		<div class="column is-2">
			<input v-model="quantity" required type="number" min="1"
				   :name="`${name}[${index}][quantity]`" class="input">
		</div>
		<div class="column is-2">
			<input v-model="unitPrice" required type="number" min="0"
				   :name="`${name}[${index}][unitPrice]`" class="input">
		</div>
		<div class="column">
			<div class="dropdown is-hoverable w-100">
				<div class="dropdown-trigger fill-parent">
					<input v-model="item" required
						   :name="`${name}[${index}][item]`" class="input">
				</div>
				<div class="dropdown-menu">
					<div class="dropdown-content">
						<a class="dropdown-item" v-for="(option, index) in options" :key="index" v-text="option.item"
						   @click="updateValue(option)"></a>
					</div>
				</div>
			</div>
		</div>
		<div class="column is-2" v-text="localNumber(total)">
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
		mixins: [DatatableFormatters],
		props: {
			name: {
				type: String,
				required: true
			},
			value: {
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
			}
		},

		data() {
			return {
				quantity: this.value.quantity,
				unitPrice: this.value.unitPrice,
				item: this.value.item,
				totalVal: 0
			}
		},

		beforeDestroy() {
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
				if (this.value.quantity && this.value.unitPrice) {
					val = this.value.quantity * this.value.unitPrice;
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
				this.$emit('input', {
					quantity: this.quantity,
					unitPrice: this.unitPrice,
					item: this.item,
				});
			},
			item() {
				this.$emit('input', {
					quantity: this.quantity,
					unitPrice: this.unitPrice,
					item: this.item,
				});
			},
			unitPrice(value) {
				if (value < 0) {
					this.unitPrice = 0;
				}
				this.$emit('input', {
					quantity: this.quantity,
					unitPrice: this.unitPrice,
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

		&.is-2 {
			min-width: 75px;
		}
	}
</style>