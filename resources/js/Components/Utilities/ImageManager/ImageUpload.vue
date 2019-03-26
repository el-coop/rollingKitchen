<template>
	<div>
		<div class="file has-name is-medium is-light is-boxed is-fullwidth">
			<label class="file-label">
				<upload-component name="photo" :multiple="true" :drop="true" v-model="files"
								  ref="upload" :post-action="url" :accept="accept" :data="data"
								  @input-file="updateFiles"></upload-component>
				<span class="file-cta">
					<span class="file-icon">
						<font-awesome-icon icon="file-upload"></font-awesome-icon>
					</span>
					<span class="file-label has-text-centered" v-text="$translations.uploadPhotos">
					</span>
				</span>
				<span class="file-name" v-for="file in files">
					<span class="uploading" v-if="file.active"><button
							class="button is-light is-loading is-fullwidth"></button></span>
					<span class="level is-mobile h-100 has-items-aligned-center">
						<span class="level-left h-100">
							<span class="level-item">
								<img v-if="file.name.indexOf('.pdf') < 0" class="image is-max37-max64" :src="generateThumbnail(file)">
							</span>
							<span v-text="file.name" class="level-item"></span>
						</span>
						<span class="level-right h-100">
							<span class="level-item">
								<button class="button is-danger is-inverted" type="button"
										@click.prevent="$refs.upload.remove(file)">
									<font-awesome-icon
											icon="times-circle">
									</font-awesome-icon>
								</button>
							</span>
						</span>
					</span>
				</span>
			</label>
		</div>
		<button type="button" class="button is-primary is-fullwidth" v-if="files.length" :disabled="$refs.upload.active"
				@click.prevent="$refs.upload.active = true" v-text="$translations.upload">
		</button>
	</div>
</template>

<script>
	import UploadComponent from 'vue-upload-component';

	export default {
		name: "ImageUpload",
		components: {
			UploadComponent
		},

		props: {
			url: {
				type: String,
				required: true
			},
			data: {
				type: Object,
				default() {
					return {};
				}
			}

		},

		data() {
			return {
				files: [],
				accept: 'img/*',
			}
		},

		methods: {
			generateThumbnail(file) {
				return URL.createObjectURL(file.file);
			},

			updateFiles(newFile, oldFile) {
				if (!newFile || !oldFile) {
					return;
				}
				if (newFile.error && !oldFile.error) {
					this.$toast.error(this.$translations.tryLater, this.$translations.operationFiled);
				}
				if (newFile.success && !oldFile.success) {
					if (typeof newFile.response === "string") {
						this.$toast.error(this.$translations.tryLater, this.$translations.operationFiled);
					}
					this.$emit('uploaded', newFile.response);
					this.$refs.upload.remove(newFile);
				}
			},

		}

	}
</script>

<style scoped>
	.image.is-max37-max64 {
		width: auto;
		height: auto;
		max-width: 64px;
		max-height: 37px;
	}

	.uploading {
		text-align: center;
		position: absolute;
		width: 100%;
		height: 100%;
	}
</style>
