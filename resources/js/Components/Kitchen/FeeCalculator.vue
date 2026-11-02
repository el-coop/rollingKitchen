<template>
    <div>
        <div class="box">
            <label class="label" v-text="$translations.services"></label>
            <div class="table-container">
                <table class="table is-fullwidth">
                    <thead>
                    <tr>
                        <th v-text="$translations.name"></th>
                        <th v-text="$translations.amount"></th>
                        <th v-text="$translations.number" class="is-hidden-phone"></th>
                        <th v-text="$translations.total"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="service in this.services" :key="service.id">
                        <td>
                            {{ service.service }}
                            <div v-if="service.type === 2">
                                <div
                                    v-for="(condition, index) in service.conditions"
                                    v-text="condition['name_' + lang]"
                                    class="has-text-danger is-size-7"
                                    :key="index">
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ '€' + formatEstimation(service.price) }}
                            <div v-if="service.type === 2">
                                <div
                                    v-for="(condition, index) in service.conditions"
                                    v-text="'€' + formatEstimation(condition.price)"
                                    class="has-text-danger is-size-7"
                                    :key="index">
                                </div>
                            </div>
                        </td>
                        <td v-text="service.amount" class="is-hidden-phone"></td>
                        <td v-text="'€' + formatEstimation(service.total)"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="is-flex is-justify-content-end">
                <label
                    v-text="$translations.totalServices + ': €' + formatEstimation(serviceTotal) + ' ' +$translations.excludingVAT"
                    class="label"></label>
            </div>
        </div>
        <div class="box">
            <h5 class="is-size-5 has-text-weight-bold" v-text="$translations.stagingFee"/>

            <label class="label" v-text="$translations.revenueIncluding"/>
            <input type="number" min="0" class="input" v-model="estimate">
            <div v-text="$translations.revenueExcluding  + ' €' + formatEstimation(estimateExcluding)"/>
            <div class="table-container">
                <table class="table is-fullwidth">
                    <thead>
                    <tr>
                        <th v-text="`${$translations.level} ${$translations.revenueExcluding}`"/>
                        <th v-text="$translations.amount"/>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="tier in stagingTiers" :key="tier.label">
                        <td v-text="$translations[tier.label]"/>
                        <td v-text="'€' + formatEstimation(tier.amount)"/>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="is-flex is-justify-content-end">
                <label class="label"
                       v-text="$translations.totalStaging + ': €' + formatEstimation(revenueTotal) + ' ' +$translations.excludingVAT"/>
            </div>
            <div class="is-flex is-justify-content-end" v-text="$translations.stagingMin"></div>
            <div class="is-flex is-justify-content-end" v-text="$translations.totalStaging + '  is %' + formatEstimation(percentageOfRevenue) + ' ' + $translations.yourRevenue"></div>
        </div>
        <div class="box" v-if="sellsDrinks">
            <div class="is-flex is-justify-content-end">
                <label class="label"
                       v-text="$translations.drinksFee + ': €' + formatEstimation(drinksTotal) + ' ' + $translations.excludingVAT"/>
            </div>
        </div>
        <div class="is-flex is-justify-content-end">
            <div>
                <div class="is-size-4"
                     v-text="$translations.totalRegistration + ': €' + formatEstimation(total) + ' ' +$translations.excludingVAT"/>
            </div>
        </div>
    </div>
</template>

<script>
import StagingFeeMixin from "../Utilities/StagingFeeMixin";

export default {
    name: "FeeCalculator",
    mixins: [StagingFeeMixin],
    props: {
        initServices: {
            type: Array,
            required: true
        },
        initSellsDrinks: {
            type: Boolean,
            default: false
        },
        drinksFee: {
            type: Number,
            default: 0
        },
        registrationYear: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
            estimate: 0,
            services: this.initServices,
            sellsDrinks: this.initSellsDrinks,
            lang: 'nl'
        }
    },
    computed: {
        estimateExcluding() {
            return this.estimate / 1.09;
        },
        stagingTiers() {
            return this.stagingFeeTiers(this.estimateExcluding, this.registrationYear);
        },
        revenueTotal() {
            let total = this.stagingTiers.reduce((sum, tier) => sum + tier.amount, 0);
            if (total < 1500){
                return 1500;
            }
            return total;
        },
        drinksTotal() {
            return this.sellsDrinks ? this.drinksFee : 0;
        },
        total() {
            return this.revenueTotal + this.serviceTotal + this.drinksTotal;
        },
        percentageOfRevenue() {
            if (this.estimate === 0) {
                return 0
            }
            return (this.revenueTotal / this.estimateExcluding) * 100;
        },
        serviceTotal() {
            let total = 0;
            this.services.forEach((service) => {
                total += service.total;
                if (service.type === 2) {
                    let sortedConditions = [...service.conditions]
                        .sort((a, b) => parseFloat(a.limit) - parseFloat(b.limit));
                    let extra = 0;
                    sortedConditions.forEach((condition) => {
                        if (this.estimateExcluding >= parseFloat(condition.limit)) {
                            extra = condition.price;
                        }
                    });
                    total += parseFloat(extra);
                }
            });

            return total;
        }
    },
    methods: {
        formatEstimation(value) {
            let num = new Intl.NumberFormat(document.documentElement.lang, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(value);
            return num;
        },
        equivalentName(service, price) {
            let condition = (service.conditions || []).find(condition => parseFloat(condition.price) === parseFloat(price));
            return condition ? condition['name_' + document.documentElement.lang] : service['name_' + document.documentElement.lang];
        },
        updateSellsDrinks(e) {
            if (e.target.name === 'sells_drinks') {
                this.sellsDrinks = e.target.value === '1';
            }
        },
        updateService(e, changedService) {
            if (this.services.some(service => service.id === changedService.id)) {
                if (e.target.type === 'checkbox') {
                    this.services = this.services.filter(service => service.id !== changedService.id);
                } else if (e.target.type === 'radio'){
                    let index = this.services.findIndex(service => service.id === changedService.id);
                    this.services[index].service = this.equivalentName(changedService, e.target.value);
                    this.services[index].price = e.target.value;
                    this.services[index].total = parseFloat(e.target.value);
                } else {
                    if (e.target.value < 1) {
                        this.services = this.services.filter(service => service.id !== changedService.id);
                    } else {
                        let index = this.services.findIndex(service => service.id === changedService.id);
                        this.services[index].amount = e.target.value;
                        this.services[index].total = e.target.value * parseFloat(changedService.price);
                    }
                }
            } else {
                let price = parseFloat(changedService.price);
                if (e.target.type === 'radio') {
                    price =  e.target.value;
                }
                this.services.push({
                    service: e.target.type === 'radio' ? this.equivalentName(changedService, price) : changedService['name_' + document.documentElement.lang],
                    amount: 1,
                    price: price,
                    total: parseFloat(price),
                    id: changedService.id
                })
            }
        }
    },
    mounted() {
        this.lang = document.documentElement.lang || 'nl';
    }
}
</script>
