<template>
    <div>
        <a :href="link">
            <slot/>
        </a>
        <div class="field is-horizontal">
            <div class="field-label is-normal has-text-left mr-half">
                <label class="label">After date (optional)</label>
            </div>
            <p class="control">
                <input v-model="value" type="date" class="input">
            </p>
        </div>
    </div>
</template>

<script>
	export default {
		name: "LinkDateSelector",
		props: {
			href: {
				type: String,
				required: true
			},
			name: {
				type: String,
				required: true
			},
		},

		data() {
			return {
				value: '',
			}
		},

		computed: {
			link() {
				let link = this.href;
				if(this.value){
					link += `?${this.name}=${this.value}`
                }
				return link;
			}
		}
	}
</script>

<style scoped>
    .field {
        max-width: 21em
    }
</style>
