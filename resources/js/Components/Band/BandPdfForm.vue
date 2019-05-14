<template>
	<div>
		<h4 class="title is-4" v-text="$translations.technicalRequirements"></h4>
		<h6 class="subtitle is-6" v-if="hasPdf">
			<a :href="url" v-text="$translations.download"></a>
		</h6>
		<dynamic-form :init-fields="[{
			name: 'file',
			type: 'file',
			label: $translations.chooseFile,
		}]" method="post" :headers="{'Content-Type': 'multipart/form-data'}" :button-text="$translations.upload"
					  :url="url" @object-update="handleSubmit">
		</dynamic-form>
	</div>
</template>

<script>
	export default {
		name: "BandBdfForm",

		props: {
			initHasPdf: {
				type: Boolean,
				required: true
			},

			bandId: {
				type: String,
				required: true
			}
		},

		data() {
			return {
				hasPdf: this.initHasPdf
			}
		},

		methods: {
			handleSubmit() {
				this.hasPdf = true;
				this.$emit('pdf-uploaded');
			}
		},

		computed: {
			url() {
				return `/band/${this.bandId}/pdf`;
			}
		}
	}
</script>
