<template>
	<div class="list-item">
		<div class="level is-mobile">
			<div class="level-left">
				<div class="level-item">
					<img class="image is-max37-max64" :src="image.url">
				</div>
			</div>
			<div class="level-right h-100">
				<div class="level-item">
					<button class="button is-danger is-inverted" :class="{'is-loading': deleting}" type="button"
							@click="deleteImage">
						<font-awesome-icon
								icon="times-circle">
						</font-awesome-icon>
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		name: "ImageEntry",
		props: {
			image: {
				type: Object,
				required: true
			},
			deleteUrl: {
				type: String,
				default: ''
			},
		},

		data() {
			return {
				deleting: false
			}
		},

		methods: {
			async deleteImage() {
				this.deleting = true;
				try {
					axios.delete(`${this.deleteUrl}/${this.image.id}`);
					this.$emit('deleted', this.image);
				} catch (error) {
					console.log(error);
					this.$toast.error('Please try again later', 'Operation failed');
				}
				this.deleting = false;
			}
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
</style>