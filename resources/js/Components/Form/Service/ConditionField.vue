    <template>
        <div>
            <label class="label" v-text="field.label"></label>
            <div class="control">
                <div class="" :class="{'is-danger': error}">
                    <div class="columns" v-for="(condition, index) in conditions" :key="index">
                        <div class="column">
                            <label class="label">{{ this.$translations.name }}
                            </label>
                            <div class="control">
                                <input class="input" type="text"
                                       :name="`conditions[${index}][name_en]`"
                                       @enter.prevent
                                       required
                                       v-model="condition.name_en"
                                >
                            </div>
                            <p v-if="error" class="help is-danger" v-text="errorText"></p>
                        </div>
                        <div class="column">
                            <label class="label">{{ this.$translations.name_nl }}
                            </label>
                            <div class="control">
                                <input class="input" type="text"
                                       :name="`conditions[${index}][name_nl]`"
                                       @enter.prevent
                                       required
                                       v-model="condition.name_nl"
                                >
                            </div>
                            <p v-if="error" class="help is-danger" v-text="errorText"></p>
                        </div>
                        <div class="column">
                            <label class="label">{{ this.$translations.price }}
                            </label>
                            <div class="control">
                                <input class="input" type="number"
                                       :name="`conditions[${index}][price]`"
                                       @enter.prevent
                                       required
                                       v-model="condition.price"
                                >
                            </div>
                            <p v-if="error" class="help is-danger" v-text="errorText"></p>
                        </div>
                        <div class="column" v-if="field.serviceType === 2">
                            <label class="label">{{ this.$translations.limit }}
                            </label>
                            <div class="control">
                                <input class="input" type="number"
                                       :name="`conditions[${index}][limit]`"
                                       @enter.prevent
                                       required
                                       v-model="condition.limit"
                                >
                            </div>
                            <p v-if="error" class="help is-danger" v-text="errorText"></p>
                        </div>
                        <div class="column is-flex is-align-items-flex-end">
                            <button type="button"  class="button is-danger" @click="removeCondition(index)" v-text="this.$translations.delete"></button>
                        </div>
                    </div>
                </div>
                <p v-if="error" class="help is-danger" v-text="errorText"></p>
            </div>
            <button type="button m-1" class="button is-light" @click="addCondition" v-text="this.$translations.add"></button>
        </div>
    </template>
    <script>
    import FieldMixin from '../FieldMixin';

    export default {
        name: 'ConditionField',
        mixins: [FieldMixin],
        data() {
            return {
                conditions: this.field.value.length !== 0
                    ? this.field.value.map(s => ({
                        ...s,
                        _id: s._id ?? (Date.now() + Math.random()) // add only if missing
                    }))
                    : [{
                        _id: Date.now() + Math.random(),
                        name_en: '',
                        name_nl: '',
                        price: '',
                        limit: ''
                    }]
            }
        },
        methods: {
            addCondition() {
                this.conditions.push({_id: Date.now() + Math.random(), name_en: '', name_nl: '', value: '', limit: ''})
            },
            removeCondition(index) {
                this.conditions.splice(index, 1);
            }
        }
    }
    </script>

    <style scoped>
    .label {
        white-space: nowrap;
    }
    </style>
