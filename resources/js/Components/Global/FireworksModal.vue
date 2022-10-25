<template>
    <ModalComponent name="fireworks" :open="open" @close="open=false">
        <div class="has-text-centered is-size-4" v-html="text">
        </div>
        <figure class="image is-4by3">
            <img src="/images/proost.gif">
        </figure>
    </ModalComponent>
</template>

<script>
import ModalComponent from "../Utilities/ModalComponent";

export default {
    name: "fireworks-modal",
    components: {ModalComponent},
    props: {
        text: {
            type: String,
            default: ''
        }
    },

    data(){
        return {
            open: true
        }
    }

}
</script>
