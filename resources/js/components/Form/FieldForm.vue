<template>
    <form method="POST">
        <slot name="csrf"></slot>
        <slot name="method" v-if="editField !== null"></slot>
        <div class="field">
            <label class="label">Name</label>
            <div class="control">
                <input class="input" type="text" name="name" v-model="form.name" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Dutch Name</label>
            <div class="control">
                <input class="input" type="text" placeholder="name" name="name_nl" v-model="form.name_nl"
                       required>
            </div>
        </div>
        <div class="field">
            <label class="label">Type</label>
            <div class="control">
                <div class="select">
                    <select name="type" v-model="form.type" required>
                        <option :selected="isSelected(typename)" v-for="(typename, index) in types">{{typename}}
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <div v-if="form.type == 'checkbox'" class="field">
            <label class="label">Options</label>
            <div v-for="option in options" class="field">
                <div class="control ">
                    <input type="text" class="input" name="options[]" v-model="option.value" required/>
                </div>
            </div>
            <div class="control">
                <button v-on:click="addOption" type="button" class="button is-success">Add</button>
                <button v-if="options.length > 1" v-on:click="removeOption" type="button" class="button is-danger">Remove</button>
            </div>
        </div>
        <input name="form" hidden :value="fieldForm"/>
        <div class="field">
            <div class="control">
                <button type="submit" :formaction="action" class="button is-info">
                    {{this.btn}}
                </button>
            </div>
        </div>
    </form>
</template>

<script>
    export default {
        name: "field-form",
        props: {
            fieldForm: {
                type: String,
                required: true
            },
            editField: {
                required: true
            }
        },
        data() {
            return {
                type: '',
                options: [{value: ''}],
                types: ['checkbox', 'text', 'textarea'],
                form: {
                    name: '',
                    name_nl: '',
                    type: ''
                },
                btn: 'Create',
                action: '/admin/field'
            }
        },
        methods: {
            addOption() {
                this.options.push({ value: '' })
            },
            removeOption() {
                this.options.pop();
            },
            isSelected(typename) {
                return this.type === typename;
            }
        },
        mounted() {
            if (this.editField) {
                this.action = '/admin/field/' + this.editField.id;
                this.btn = 'Edit';
                this.form = this.editField;
                if (this.editField.type === 'checkbox') {
                    this.options.pop();
                    this.editField.options.forEach((option) => {
                        this.options.push({value: option});
                    });
                }
            }
        }
    }
</script>
