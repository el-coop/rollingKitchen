<template>
    <div class="modal" :class="{'is-active': active}">
        <div class="modal-background" @click="$emit('update:active',false)"></div>
        <div class="modal-content" :style="{top, left}" ref="content">
            <div class="card" :class="bodyClass" :style="{height: this.modalHeight, width: this.modalWidth}">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "Modal",

    props: {
        active: {
            type: Boolean,
            default: false
        },
        bodyClass: {
            type: String,
            default: ''
        },
        height: {
            type: String,
            default: 'auto'
        },
        width: {
            type: String,
            default: '600'
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

    computed: {
        modalWidth() {
            if (isNaN(this.width)) {
                return this.width;
            }

            return `${this.width}px`;
        },
        modalHeight() {
            if (isNaN(this.height)) {
                return this.height;
            }

            return `${this.height}px`;
        },
        top() {
            const percent = this.pivotY * 100;
            let height = this.height;
            if(height === 'auto'){
                height = this.$refs.content.offsetHeight;
            }
            height = parseFloat(height);

            return  `calc(${percent}vh - ${this.pivotY * height}px)`;
        },
        left() {
            const percent = this.pivotX * 100;
            let width = this.width;
            if(width === 'auto'){
                width = this.$refs.content.offsetWidth;
            }
            width = parseFloat(width);

            return `calc(${percent}vw - ${this.pivotX * width}px)`;
        }
    }
}
</script>

<style>
.modal-content {
    max-height: unset;
    position: absolute;
}

.modal-background {
    background-color: rgba(0, 0, 0, 0.2);
}
</style>
