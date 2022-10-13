<template>
    <Modal height="100%" :pivotY="0" :pivotX="1" v-model:active="open" @update:active="$emit('close')">
        <div class="modal-body box">
            <button class="modal-close is-large" @click="$emit('close')"/>
            <slot/>
        </div>
    </Modal>
</template>

<script>

import Modal from '../Modal';

export default {
    name: "DatatableRowDisplay",
    emits: ['close'],
    components: {Modal},
    props: {
        open: {
            default: ''
        },
        width: {
            default: 600
        }
    },
}
</script>

<style scoped lang="scss">
@import "../../../../sass/variables";

.modal-body {
    height: 100vh;
    overflow-x: hidden;
    overflow-y: auto;

    > *:last-child {
        margin-bottom: 1rem;
    }

    .modal-close {
        position: relative;
        float: right;
        top: 0;
        right: 0;

        &:after, &:before {
            background: $contrast-bg;
        }
    }
}
</style>
