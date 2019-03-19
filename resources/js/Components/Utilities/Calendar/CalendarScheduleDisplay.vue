<template>
    <div class="tags">
		<span v-for="(entry, index) in entries" class="is-hidden">
			<input :key="`input_band_${index}`" type="hidden"
                   :value="entry.band"
                   :name="`calendar[${dateTime}][${index}][band]`">
			<input :key="`input_stage_${index}`" type="hidden"
                   :value="entry.stage"
                   :name="`calendar[${dateTime}][${index}][stage]`">
			<input :key="`input_payment_${index}`" type="hidden"
                   :value="entry.payment"
                   :name="`calendar[${dateTime}][${index}][payment]`">
		</span>
        <div class="tags has-addons" v-for="entry in entries" :key="entry.stage"
             @click.stop="openModal({id: entry.band,stage: entry.stage,payment: entry.payment})">
			<span class="tag" :class="approveStatus(entry)"
                  v-text="stages[entry.stage]"></span>
            <span class="tag is-dark" v-text="bands[entry.band]"></span>
            <a class="tag is-delete" @click.stop="remove(entry.stage)"></a>
        </div>
    </div>
</template>

<script>
    export default {
        name: "CalendarScheduleDisplay",
        props: {
            data: {
                type: Object,
                required: true
            },
            bands: {
                type: Object,
                required: true
            },
            stages: {
                type: Object,
                required: true
            },
            edit: {
                type: Function,
                required: false
            },
            onUpdate: {
                type: Function,
                required: true
            },
            init: {
                type: Array,
                default() {
                    return [];
                }
            },
            dateTime: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                entries: [...this.init].sort((a, b) => {
                    if (this.stages[a.stage] > this.stages[b.stage]) {
                        return 1;
                    }
                    return -1;
                }),
            }
        },
        methods: {
            remove(id, key = 'stage') {
                const index = this.entries.findIndex((entry) => {
                    return entry[key] === id;
                });
                if (index < 0) {
                    return;
                }
                this.onUpdate(-this.entries.splice(index, 1)[0].payment);
            },
            openModal(payload) {
                this.edit(payload);
            },
            approveStatus(show) {
                console.log(show);
                if (this.init.some((initShow) => {
                    return initShow.approved === 'accepted' && initShow.band === show.band && parseFloat(initShow.payment) === parseFloat(show.payment);
                })) {
                    return 'is-info';
                }
                if (show.approved === 'rejected'){
                    return 'is-danger';
                }
                return 'is-warning'
            }

        },
        watch: {
            data(value) {
                if (!value.band) {
                    return;
                }
                this.remove(value.stage);
                this.remove(value.band, 'band');
                this.entries.push(value);
                this.entries.sort((a, b) => {
                    if (this.stages[a.stage] > this.stages[b.stage]) {
                        return 1;
                    }
                    return -1;
                });
                this.onUpdate(parseFloat(value.payment));
            }
        }
    }
</script>

<style scoped lang="scss">
    .tags:not(:last-child) {
        margin-bottom: 0;
    }
    .tags {
        cursor: pointer;
    }
</style>
