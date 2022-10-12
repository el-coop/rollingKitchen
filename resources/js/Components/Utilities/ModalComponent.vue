<template>
    <Modal :name="name" transition="slide-up-opacity"
                     :fit-parent="true"
                     classes="modal--container"
                     content-class="modal--content"
                     :content-style="{height: 'auto',width}"
                     :pivotY="pivotY"
                     :pivotX="pivotX"
                     ref="modal">
        <div class="box h-100 modal-body">
            <button class="modal-close is-large" type="button" @click="$modal.hide(name)"></button>
            <slot/>
        </div>
    </Modal>
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
            default: 600
        },
        height: {
            default: 'auto'
        },
        pivotY: {
            type: Number,
            default: 0.5
        },
        pivotX: {
            type: Number,
            default: 0.5
        }
    },
    data() {
        return {
            isOpen: false
        };
    }
}
</script>

<style lang="scss">
@import "../../../sass/variables";

.modal {
    &--content {
        // default settings of vue-js-modal
        position: relative;
        overflow: hidden;
        box-sizing: border-box;
        background-color: white;
        border-radius: 3px;
        box-shadow: 0 20px 60px -2px rgba(27, 33, 58, 0.4);
        width: 600px;

        // step-to-step settings of vue-final-modal
        display: flex;
        flex-direction: column;
        //margin: 0 1rem;
        //padding: 1rem;
        //border: 1px solid #e2e8f0;
        //border-radius: 0.25rem;
        //background: #fff;
    }

    &--container {
        // step-to-step settings of vue-final-modal
        display: flex;
        justify-content: center;
        align-items: center;
    }
}

.modal-body {
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
