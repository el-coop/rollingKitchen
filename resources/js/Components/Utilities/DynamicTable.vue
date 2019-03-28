<template>
	<div>
		<slot :data="fields"></slot>
		<div class="table-container">
			<table class="table is-fullwidth">
				<thead>
				<tr>
					<th v-for="(column,index) in columns" v-text="column.label" :key="index"
						v-if="!column.invisible" :class="{'is-hidden-phone': column.responsiveHidden}"></th>
					<th v-if="hasActions">
					</th>
					<th v-if="action"></th>
				</tr>
				</thead>
				<draggable tag="tbody" :list="fields" :disabled="!sortable">
					<tr v-for="(field, index) in fields" :key="`${index}${field.id}`">
						<td v-if="!column.invisible" v-for="(column,colIndex) in columns" :key="`${index}_${colIndex}`"
							v-html="valueDisplay(column,field[column.name])" @click="editObject(field)"
							:class="{'is-hidden-phone': column.responsiveHidden}"></td>
						<td v-if="hasActions">
							<slot name="actions" :field="field" :on-update="replaceObject"></slot>
						</td>
						<td v-if="action && deleteAllowed">
							<button class="button is-danger" type="button"
									:class="{'is-loading' : deleteing === field.id}"
									:disabled="field.status === 'protected'"
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
		<modal-component :name="`${_uid}modal`" v-if="action" :width="modal.width" :height="modal.height"
						 :pivotX="modal.pivotX" :pivotY="modal.pivotY">
			<dynamic-form :headers="headers" :init-fields="!formFromUrl ? formFields : null" :method="method" :url="url"
						  @object-update="updateObject" :extra-data="extraData" :button-text="formButtonText">

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
			formButtonText: {
				type: String,
				default() {
					return this.$translations.save;
				}

			},

			modal: {
				type: Object,
				default() {
					return {
						height: 'auto',
						width: 600,
						pivotX: 0.5,
						pivotY: 0.5,
					}
				}
			},

			formFromUrl: {
				type: Boolean,
				default: false
			},

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
			deleteAllowed: {
				type: Boolean,
				default: true
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
			},

			sortBy: {
				type: String
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

		mounted() {
			this.sort();
		},

		methods: {
			sort() {
				if (this.sortBy) {
					this.fields.sort((a, b) => {
						a = a[this.sortBy].split(':');
						b = b[this.sortBy].split(':');

						if (parseInt(a[0]) < parseInt(b[0])) {
							return -1;
						}
						if (parseInt(a[0]) > parseInt(b[0])) {
							return 1;
						}
						if (parseInt(a[1]) < parseInt(b[1])) {
							return -1;
						}
						if (parseInt(a[1]) > parseInt(b[1])) {
							return 1;
						}

						return 0;
					});
				}
			},

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
					const callbacks = column.callback.split('|');
					callbacks.forEach((callback) => {
						value = this[callback](value, column);
					});
				}
				return value;
			},

			replaceObject(object) {
				const editedId = this.fields.findIndex((item) => {
					return item.id === object.id;
				});
				this.fields.splice(editedId, 1, object);
				if (this.sortBy) {
					this.sort();
				}
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
				if (this.sortBy) {
					this.sort();
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

			formFields() {
				const fields = [];

				for (const prop in this.columns) {
					const column = this.columns[prop];
					if (!Object.keys(this.object).length || column.edit !== false) {
						fields.push({
							name: column.name,
							label: column.label,
							value: typeof this.object[column.name] === 'undefined' ? '' : this.object[column.name],
							type: column.type || 'text',
							subType: column.subType || '',
							options: column.options || {},
							icon: column.icon || false,
							hideLabel: column.hideLabel || false
						});
					}
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

			hasActions() {
				return !!this.$scopedSlots.actions;
			}

		},
	}
</script>
