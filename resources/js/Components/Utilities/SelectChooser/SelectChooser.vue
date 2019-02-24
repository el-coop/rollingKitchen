<template>
	<div>
		<div class="field">
			<div class="control">
				<div class="select is-fullwidth">
					<select v-model="selected">
						<option v-for="(view, index) in views" :value="index" v-text="view.label"></option>
					</select>
				</div>
			</div>
		</div>
		<div>
			<slot></slot>
		</div>
	</div>
</template>

<script>

	export default {
		name: "SelectChooser",

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
		},

	}
</script>
