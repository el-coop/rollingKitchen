<template>
	<div>
		<ul>
			<li v-for="file in files" v-text="file"></li>
		</ul>
		<input type="file"  name="photos" accept="image/*" multiple="true" id="photos">
		<label for="photos"><button class="button is-primary" type="button" >Add File</button></label>
	</div>
</template>

<script>
	export default {
		name: "PhotoUpload",

		data() {
			return {
				files: []
			}
		},

	}
</script>

<style scoped>

</style>