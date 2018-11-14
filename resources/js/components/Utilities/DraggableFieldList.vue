<template>
    <draggable :list="modelFields" :element="'tbody'" @end="getOrder">
        <tr v-for="field in modelFields" :key="field.order">
            <td>{{field.name}}</td>
            <td>{{field.name_nl}}</td>
            <td>{{field.type}}</td>
            <td>
                <form method="POST">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" :value="csrf"/>
                    <div class="control">
                        <button type="submit" class="button is-danger" :formaction="'/admin/field/' + field.id">
                            {{deleteBtn}}
                        </button>
                    </div>
                </form>
            </td>
            <td>
                <button v-on:click="openEdit(field)" class="button is-dark">{{editBtn}}
                </button>
            </td>
        </tr>
    </draggable>
</template>

<script>
    import draggable from 'vuedraggable'

    export default {
        name: "DraggableFieldList",
        components: {
            draggable
        },
        props: {
            givenFields: {
                required: true
            },
            deleteBtn: {
                type: String,
                required: true
            },
            editBtn: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                modelFields: this.givenFields,
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        },
        methods: {
            openEdit(field) {
                this.$bus.$emit('open-edit-modal', field)
            },
            getOrder() {
                let order = [];
                this.modelFields.forEach((field) => {
                    order.push(field.id)
                });
                this.$bus.$emit('get-order', order);
            },
        },
    }
</script>

<style scoped>

</style>
