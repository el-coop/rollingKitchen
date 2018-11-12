<template>
    <modal :name="name" transition="slide-up-opacity" :height="height" :adaptive="true"
           :width="width"
           :scrollable="true"
           :pivotY="pivotY"
           ref="modal">
        <div class="card">
            <div class="card-content">
                <slot/>
            </div>
        </div>
    </modal>
</template>

<script>
    export default {
        name: "modal-component",
        props: {
            name: {
                type: String,
                required: true
            },
            width: {
                type: Number,
                default: 600
            },
        },
        data() {
            return {
                pivotY: 0.5,
                height: 'auto',
                isOpen: false
            };
        }
    }
</script>