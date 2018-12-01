<template>
	<div class="columns is-mobile">
		<div class="column is-2">
			<input v-model="quantity" required
				   :name="`${name}[][quantity]`" class="input">
		</div>
		<div class="column is-2">
			<input v-model="unitPrice" required
				   :name="`${name}[][unitPrice]`" class="input">
		</div>
		<div class="column">
			<div class="dropdown is-hoverable w-100">
				<div class="dropdown-trigger fill-parent">
					<input v-model="item" required
						   :name="`${name}[][item]`" class="input">
				</div>
				<div class="dropdown-menu">
					<div class="dropdown-content">
						<a class="dropdown-item" v-for="(option, index) in options" :key="index" v-text="option.name"
						   @click="updateValue(option)"></a>
					</div>
				</div>
			</div>
		</div>
		<div class="column is-2" v-text="total">
		</div>
		<div class="column is-2">
			<button class="button is-danger" @click="remove" v-text="$translations.delete"
					type="button"></button>
		</div>
	</div>
</template>

<script>
	export default {
		name: "InvoiceLine",
		props: {
			name: {
				type: String,
				required: true
			},
			data: {
				type: Object,
				required: true
			},
			options: {
				type: Array,
				required: true
			}
		},

		data() {
			return {
				quantity: this.data.quantity,
				unitPrice: this.data.unitPrice,
				item: this.data.item,
				totalVal: 0
			}
		},

		beforeDestroy() {
			this.$emit('total', 0);
		},

		methods: {
			updateValue(option) {
				this.unitPrice = option.unitPrice;
				this.item = option.name;
			},

			remove() {
				this.$emit('remove');
			}
		},

		computed: {
			total() {
				let val = 0;
				if (this.quantity && this.unitPrice) {
					val = this.quantity * this.unitPrice;
				}
				if (val != this.totalVal) {
					this.$emit('total', val);
					this.totalVal = val;
				}
				return val;

			}
		},

	}
</script>

<style scoped>
	.w-100 {
		width: 100%;
	}
</style>