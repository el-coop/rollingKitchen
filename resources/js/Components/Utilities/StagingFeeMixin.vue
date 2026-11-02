<script>
const TIER_FOUR_FROM_YEAR = 2027;

export default {
    methods: {
        stagingFeeBrackets(year) {
            if (parseInt(year) >= TIER_FOUR_FROM_YEAR) {
                return [
                    {label: 'firstTen', from: 0, to: 10000, rate: 0.15},
                    {label: 'tenToTwenty', from: 10000, to: 20000, rate: 0.2},
                    {label: 'twentyToThirty', from: 20000, to: 30000, rate: 0.25},
                    {label: 'overThirty', from: 30000, to: Infinity, rate: 0.3},
                ];
            }
            return [
                {label: 'firstTen', from: 0, to: 10000, rate: 0.15},
                {label: 'tenToTwenty', from: 10000, to: 20000, rate: 0.2},
                {label: 'overTwenty', from: 20000, to: Infinity, rate: 0.25},
            ];
        },
        stagingFeeTiers(revenueExcluding, year) {
            return this.stagingFeeBrackets(year).map((bracket) => {
                return {
                    label: bracket.label,
                    amount: Math.max(0, Math.min(revenueExcluding, bracket.to) - bracket.from) * bracket.rate
                };
            });
        },
        stagingRevenueFromFee(fee, year) {
            let remaining = fee;
            for (const bracket of this.stagingFeeBrackets(year)) {
                const bracketMax = (bracket.to - bracket.from) * bracket.rate;
                if (remaining <= bracketMax) {
                    return bracket.from + remaining / bracket.rate;
                }
                remaining -= bracketMax;
            }
        }
    }
}
</script>
