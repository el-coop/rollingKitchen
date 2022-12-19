<template>
    <div>
        <div  class="is-flex column is-justify-content-center">
            <figure class="image logo-preview">
                <img :src="logo">
            </figure>
        </div>
        <dynamic-form @submitted="updateLogo" :headers="{'Content-Type': 'multipart/form-data'}" :url="action" headers="" method="post"  :init-fields="[{
            type: 'file',
            name: 'photo',
            label: 'Logo'
        }]">
        </dynamic-form>
    </div>
</template>

<script>
export default {
    name: "LogoForm",
    props: {
        action: {
            required: true,
            type: String
        }
    },
    data(){
        return {
            logo: '/storage/images/logo.png' + '?' + new Date().getTime()
        }
    },
    methods: {
        updateLogo(){
            this.logo = '/storage/images/logo.png' + '?' + new Date().getTime();
        }
    }
}
</script>

<style scoped>
.logo-preview {
    max-height: 200px;
}
</style>
