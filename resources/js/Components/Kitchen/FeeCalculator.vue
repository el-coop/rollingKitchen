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
                        <td v-text="service.service"></td>
                        <td v-text="'€' + formatEstimation(service.price)"></td>
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
                    <tr>
                        <td v-text="$translations.firstTen"/>
                        <td v-text="'€' + formatEstimation(toTen)"/>
                    </tr>
                    <tr>
                        <td v-text="$translations.tenToTwenty"/>
                        <td v-text="'€' + formatEstimation(tenToTwenty)"/>
                    </tr>
                    <tr>
                        <td v-text="$translations.overTwenty"/>
                        <td v-text="'€' + formatEstimation(overTwenty)"/>
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
        <div class="is-flex is-justify-content-end">
            <div>
                <div class="is-size-4"
                     v-text="$translations.totalRegistration + ': €' + formatEstimation(total) + ' ' +$translations.excludingVAT"/>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "FeeCalculator",
    props: {
        initServices: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            estimate: 0,
            services: this.initServices,
        }
    },
    computed: {
        estimateExcluding() {
            return this.estimate / 1.09;
        },
        toTen() {
            if (this.estimateExcluding < 10000) {
                return this.estimateExcluding * 0.15;
            } else {
                return 1500;
            }
        },
        tenToTwenty() {
            if (this.estimateExcluding <= 10000) {
                return 0;
            } else if (this.estimateExcluding < 20000) {
                return (this.estimateExcluding - 10000) * 0.2;
            } else {
                return 2000;
            }
        },
        overTwenty() {
            if (this.estimateExcluding <= 20000) {
                return 0;
            } else {
                return (this.estimateExcluding - 20000) * 0.25;
            }
        },
        revenueTotal() {
            let total = this.overTwenty + this.tenToTwenty + this.toTen;
            if (total < 1500){
                return 1500;
            }
            return total;
        },
        total() {
            return this.revenueTotal + this.serviceTotal;
        },
        percentageOfRevenue() {
            if (this.estimate === 0) {
                return 0
            }
            return (this.total / this.estimateExcluding) * 100;
        },
        serviceTotal() {
            let total = 0;
            this.services.forEach(function (service) {
                total += service.total;
            })
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
        updateService(e, changedService) {
            if (this.services.some(service => service.id === changedService.id)) {
                if (e.target.type === 'checkbox') {
                    this.services = this.services.filter(service => service.id !== changedService.id);
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
                this.services.push({
                    service: changedService['name_' + document.documentElement.lang],
                    amount: 1,
                    price: parseFloat(changedService.price),
                    total: parseFloat(changedService.price),
                    id: changedService.id
                })
            }
        }
    }
}
</script>
