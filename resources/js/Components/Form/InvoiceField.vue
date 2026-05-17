<template>
    <div class="field">
        <div class="box" v-if="!(field.individualTax || false)">
            <label class="label" v-text="$translations.stagingFee"/>
            <label class="label" v-text="$translations.revenueIncluding"/>
            <input type="number" min="0" class="input" v-model="stagingRevenue">
            <div v-text="$translations.revenueExcluding + ' €' + localNumber(stagingFeeEstimateExcluding)"/>
            <div class="table-container">
                <table class="table is-fullwidth">
                    <thead>
                    <tr>
                        <th v-text="`${$translations.level} ${$translations.revenueExcluding}`"/>
                        <th v-text="$translations.amount"/>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td v-text="$translations.firstTen"/>
                        <td v-text="'€' + localNumber(stagingFeeToTen)"/>
                    </tr>
                    <tr>
                        <td v-text="$translations.tenToTwenty"/>
                        <td v-text="'€' + localNumber(stagingFeeTenToTwenty)"/>
                    </tr>
                    <tr>
                        <td v-text="$translations.overTwenty"/>
                        <td v-text="'€' + localNumber(stagingFeeOverTwenty)"/>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="is-flex is-justify-content-end">
                <label class="label"
                       v-text="$translations.totalStaging + ': €' + localNumber(stagingFeeTotal) + ' ' + $translations.excludingVAT"/>
            </div>
            <input type="hidden" name="revenue" :value="hasStagingFee ? stagingFeeEstimateExcluding.toFixed(2) : ''">
            <template v-if="hasStagingFee">
                <input type="hidden" :name="`${field.name}[${values.length}][quantity]`" value="1">
                <input type="hidden" :name="`${field.name}[${values.length}][item]`" value="stagingFee">
                <input type="hidden" :name="`${field.name}[${values.length}][unitPrice]`" :value="stagingFeeTotal.toFixed(2)">
            </template>
        </div>
        <div class="box">
            <h5 class="title is-5" v-text="field.label"></h5>
            <div class="columns is-mobile">
                <div class="column"
                     :class="headerClass(header)" v-for="header in headers">
                    <h6 class="title is-6" v-text="$translations[header]"></h6>
                </div>
                <div class="column is-2"></div>
            </div>
            <InvoiceLine v-for="(entry, index) in values" :key="`${entry.unique_id}_${index}`" :index="index"
                         :name="field.name"
                         v-model="values[index]"
                         :individual-tax="field.individualTax || false"
                         :tax-options="field.taxOptions"
                         :options="field.options"
                         @total="updateTotal(index,$event)"
                         @remove="remove(index)"/>

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
                <div class="column" :class="{'is-2' : header !== 'item'}" v-for="header in headers">
                    <div v-if="header === 'unitPrice'">
                        <input v-model="extra_amount" type="number" step="0.01" @keypress.enter.prevent
                               name="extra_amount" class="input">
                    </div>
                    <div v-if="header === 'item'" class="is-fullwidth">
                        <input v-model="extra_name" @keypress.enter.prevent
                               name="extra_name" class="input">
                    </div>
                    <span v-if="header === 'total'" v-text="localNumber(extra_amount)"></span>
                </div>
                <div class="column is-2"></div>
            </div>
            <div class="columns is-mobile">
                <div class="column" :class="headerClass(header)" v-for="header in headers">
                    <span v-if="header === 'item'" v-text="$translations.total"></span>
                    <span v-if="header === 'total'" v-text="localNumber(totalSum * (1 + tax/100) + extra_amount + stagingFeeTotal)"></span>
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

        let stagingRevenue = 0;
        const stagingIndex = values.findIndex(v => v.item === 'stagingFee');
        if (stagingIndex !== -1) {
            const fee = parseFloat(values[stagingIndex].unitPrice);
            if (fee <= 1500) {
                stagingRevenue = Math.round(10000 * 1.09);
            } else if (fee <= 3500) {
                stagingRevenue = Math.round((10000 + (fee - 1500) / 0.20) * 1.09);
            } else {
                stagingRevenue = Math.round((20000 + (fee - 3500) / 0.25) * 1.09);
            }
            values.splice(stagingIndex, 1);
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
            tax,
            stagingRevenue,
            extra_name: this.field.extra_name,
            extra_amount: this.field.extra_amount === null ? 0 : parseFloat(this.field.extra_amount)
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
        },
    },

    computed: {
        totalSum() {
            return this.sum.reduce((total, num) => {
                return parseFloat(total) + parseFloat(num);
            });
        },
        stagingFeeEstimateExcluding() {
            return this.stagingRevenue / 1.09;
        },
        stagingFeeToTen() {
            if (this.stagingFeeEstimateExcluding < 10000) {
                return this.stagingFeeEstimateExcluding * 0.15;
            } else {
                return 1500;
            }
        },
        stagingFeeTenToTwenty() {
            if (this.stagingFeeEstimateExcluding <= 10000) {
                return 0;
            } else if (this.stagingFeeEstimateExcluding < 20000) {
                return (this.stagingFeeEstimateExcluding - 10000) * 0.2;
            } else {
                return 2000;
            }
        },
        stagingFeeOverTwenty() {
            if (this.stagingFeeEstimateExcluding <= 20000) {
                return 0;
            } else {
                return (this.stagingFeeEstimateExcluding - 20000) * 0.25;
            }
        },
        stagingFeeTotal() {
            if (this.stagingRevenue <= 0) {
                return 0;
            }
            let total = this.stagingFeeToTen + this.stagingFeeTenToTwenty + this.stagingFeeOverTwenty;
            if (total < 1500) {
                return 1500;
            }
            return total;
        },
        hasStagingFee() {
            return this.stagingRevenue > 0;
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
