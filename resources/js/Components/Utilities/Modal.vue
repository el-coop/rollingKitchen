<template>
    <div class="modal" :class="{'is-active': active}">
        <div class="modal-background" @click="$emit('update:active',false)"></div>
        <div class="modal-content" :style="{top, left,height: this.modalHeight, width: this.modalWidth}" ref="content">
            <div class="card" :class="bodyClass">
                <slot/>
            </div>
        </div>
    </div>
</template>

<script>
import {nextTick} from "vue";

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
            default: 'auto'
        },
        width: {
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


    data() {
        return {
            contentHeight: this.height,
            contentWidth: this.width,
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

            let height = parseFloat(this.contentHeight);

            return `calc(${percent}vh - ${this.pivotY * height}px)`;
        },
        left() {
            const percent = this.pivotX * 100;
            let width = parseFloat(this.contentWidth);

            return `calc(${percent}vw - ${this.pivotX * width}px)`;
        }
    },

    watch: {
        async active() {
            if (this.contentHeight === 'auto') {
                await nextTick();
                this.contentHeight = this.$refs.content.offsetHeight;
            }

            if (this.contentWidth === 'auto') {
                await nextTick();
                this.contentWidth = this.$refs.content.offsetWidth;
            }
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
