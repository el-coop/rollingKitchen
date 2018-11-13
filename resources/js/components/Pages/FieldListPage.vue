<script>
    export default {
        name: "field-list-page",
        props: {
            form: {
                type: String,
                required: true
            }
        },
        data(){
            return {
                object: null,
            }
        },
        methods: {
            setObject(object){
                this.object = object;
            }
        }
    }
</script>
