<template>
	<div>
		<div class="table-container">
			<table class="table is-fullwidth">
				<thead>
				<tr>
					<th v-for="(column,index) in columns" v-text="column.label" :key="index"
						v-if="!column.invisible"></th>
					<th v-if="action"></th>
				</tr>
				</thead>
				<draggable element="tbody" :list="fields" :options="draggable">
					<tr v-for="(field, index) in fields" :key="`${index}${field.id}`">
						<td v-if="!column.invisible" v-for="(column,colIndex) in columns" :key="`${index}_${colIndex}`"
							v-text="valueDisplay(column,field[column.name])" @click="editObject(field)"></td>
						<td v-if="action">
							<button class="button is-danger" type="button"
									:class="{'is-loading' : deleteing === field.id}"
									@click="destroy(field)" v-text="$translations.delete">
							</button>
						</td>
					</tr>
				</draggable>
			</table>
		</div>
		<div class="buttons" v-if="action">
			<div class="button is-success" @click="editObject({})" v-text="$translations.add">Add</div>
			<div v-if="sortable" class="button is-primary" :class="{'is-loading': savingOrder}" @click="saveOrder"
				 v-text="$translations.saveOrder"></div>
		</div>
		<modal-component :name="`${_uid}modal`" v-if="action">
			<dynamic-form :headers="headers" :init-fields="formFields" :method="method" :url="url"
						  @object-update="updateObject" :extra-data="extraData">

			</dynamic-form>
		</modal-component>
	</div>
</template>

<script>
	import draggable from 'vuedraggable'
	import DatatableFormatters from "./Datatable/DatatableFormatters";

	export default {
		name: "DynamicTable",
		components: {
			draggable
		},
		mixins: [DatatableFormatters],

		props: {
			initFields: {
				required: true,
				type: Array
			},

			columns: {
				required: true,
				type: Array
			},

			action: {
				type: String,
				default: ''
			},

			extraData: {
				type: Object,
				default() {
					return {};
				}
			},
			headers: {
				type: Object,
				default() {
					return {
						'Content-type': 'application/json'
					};
				}
			},
			edit: {
				type: Boolean,
				default: true
			},
			sortable: {
				type: Boolean,
				default: false
			}

		},

		data() {
			return {
				fields: this.initFields,
				object: {},
				deleteing: null,
				order: [],
				savingOrder: false
			}
		},

		methods: {
			editObject(field) {
				if (field.id && !this.edit) {
					return;
				}
				this.object = field;
				this.$modal.show(`${this._uid}modal`);
			},

			valueDisplay(column, value) {
				if (column.translate) {
					return this.$translations[value];
				}
				if (column.callback) {
					return this[column.callback](value);
				}
				return value;
			},

			updateObject(object) {
				if (Object.keys(this.object).length === 0) {
					this.fields.push(object);
				} else {
					const editedId = this.fields.findIndex((item) => {
						return item.id === this.object.id;
					});
					this.fields.splice(editedId, 1, object);
				}
				this.$modal.hide(`${this._uid}modal`);
			},
			async destroy(field) {
				this.deleteing = field.id;
				try {
					await axios.delete(`${this.action}/${field.id}`);
					this.fields.splice(this.fields.indexOf(field), 1);
					this.$toast.success(this.$translations.deleteSuccess);
				} catch (error) {
					this.$toast.error(this.$translations.tryLater, this.$translations.operationFiled);
				}
				this.deleteing = null;
			},

			async saveOrder() {
				this.savingOrder = true;
				const order = [];
				this.fields.forEach((item) => {
					order.push(item.id);
				});
				try {
					await axios.patch(`${this.action}/order`, {
						order
					});
					this.$toast.success(this.$translations.updateSuccess);
				} catch (error) {
					this.$toast.error(this.$translations.tryLater, this.$translations.operationFiled);
				}
				this.savingOrder = false;
			}
		},

		computed: {
			draggable() {
				return {
					disabled: !this.sortable
				}
			},

			formFields() {
				const fields = [];

				for (const prop in this.columns) {
					const column = this.columns[prop];
					fields.push({
						name: column.name,
						label: column.label,
						value: this.object[column.name] || '',
						type: column.type || 'text',
						subType: column.subType || '',
						options: column.options || {}
					});
				}

				return fields;
			},

			url() {
				if (Object.keys(this.object).length === 0) {
					return this.action;
				}

				return `${this.action}/${this.object.id}`;
			},

			method() {
				if (Object.keys(this.object).length === 0) {
					return 'post';
				}

				return 'patch';
			},

		},
	}
</script>

<style scoped>

</style>
