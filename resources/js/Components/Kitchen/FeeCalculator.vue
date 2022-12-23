<template>
    <div>
        <slot name="services">
        </slot>
        <div class="box">
            <h5 class="is-size-5 has-text-weight-bold" v-text="$translations.stagingFee"/>

            <label class="label" v-text="$translations.revenueIncluding"/>
            <input type="number" min="0" class="input" v-model="estimate">
            <div v-text="$translations.revenueExcluding  + ' €' + formatEstimation(estimateExcluding)"/>
            <div class="table-container">
                <table class="table is-fullwidth">
                    <thead>
                    <tr>
                        <th v-text="`${$translations.level} ${$translations.revenueExcluding}`" />
                        <th v-text="$translations.amount" />
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
                <label class="label" v-text="$translations.total + ': €' + formatEstimation(revenueTotal) + ' ' +$translations.excludingVAT"/>
            </div>
        </div>
        <div class="is-flex is-justify-content-end">
            <div>
                <div class="is-size-4" v-text="$translations.total + ': €' + formatEstimation(total) + ' ' +$translations.excludingVAT"/>
                <div  v-text="$translations.percentOfRevenue + ': ' + formatEstimation(percentageOfRevenue)"/>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "FeeCalculator",
    props: {
        serviceTotal: {
            required: true,
            type: Number
        }
    },
    data() {
        return {
            estimate: 0
        }
    },
    computed: {
        estimateExcluding(){
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
        revenueTotal(){
            return this.overTwenty + this.tenToTwenty + this.toTen;
        },
        total() {
            return  this.revenueTotal + this.serviceTotal;
        },
        percentageOfRevenue(){
            if (this.estimate === 0){
                return 0
            }
            return  (this.total / this.estimate) * 100;
        }
    },
    methods: {
        formatEstimation(value) {
            let num = new Intl.NumberFormat(document.documentElement.lang, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(value);
            return num;
        }
    }
}
</script>
