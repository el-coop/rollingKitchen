<template>
    <div>
        <div class="tabs is-toggle">
            <ul>
                <li v-for="(view, index) in views" :class="{'is-active': index == selected}" :key="index"
                    @click="selected = index">
                    <a>
                        <span v-text="view.label"></span>
                    </a>
                </li>
            </ul>
        </div>
        <div>
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    name: "ToggleChooser",

    props: {
        initSelected: {
            type: Number,
            default: 0
        }
    },

    data() {
        return {
            views: [],
            selected: this.initSelected
        }
    }
}
</script>
