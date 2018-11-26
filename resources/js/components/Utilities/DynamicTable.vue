<template>
	<div>
		<table class="table is-fullwidth">
			<thead>
			<tr>
				<th  v-for="(column,index) in columns" v-text="column.label" :key="index" v-if="!column.invisible"></th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			<tr v-for="(field, index) in fields" :key="`${index}${field.id}`">
				<td v-if="!column.invisible" v-for="(column,colIndex) in columns" :key="`${index}_${colIndex}`"
					v-text="field[column.name]" @click="editObject(field)"></td>
				<td>
					<button class="button is-danger" type="button" :class="{'is-loading' : deleteing === field.id}"
							@click="destroy(field)">Delete
					</button>
				</td>
			</tr>
			</tbody>
		</table>
		<div class="buttons" v-if="action">
			<div class="button is-success" @click="editObject({})">Add</div>
		</div>
		<modal-component :name="`${_uid}modal`" v-if="action">
			<dynamic-form :headers="headers" :init-fields="formFields" :method="method" :url="url"
						  @object-update="updateObject" :extra-data="extraData">

			</dynamic-form>
		</modal-component>
	</div>
</template>

<script>
	export default {
		name: "DynamicTable",

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
			}

		},

		data() {
			return {
				fields: this.initFields,
				object: {},
				deleteing: null
			}
		},

		methods: {
			editObject(field) {
			    if (field.id && !this.edit){
			        return;
				}
				this.object = field;
				this.$modal.show(`${this._uid}modal`);
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
					this.$toast.success('Delete successful');
				} catch (error) {
					this.$toast.error('Please try again later', 'Operation failed');
				}
				this.deleteing = null;
			}
		},

		computed: {
			formFields() {
				const fields = [];

				for (const prop in this.columns) {
					const column = this.columns[prop];
					fields.push({
						name: column.name,
						label: column.label,
						value: this.object[column.name] || '',
						type: column.type || 'text',
						subType: column.subType || ''
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
			}
		},
	}
</script>

<style scoped>

</style>
