<template v-if="object.id">
	<h4 class="title is-4 mt-1">
		@lang('worker/supervisor.crew'):
	</h4>
	<manage-shift-workers
			:shift="object"
			:url="`/worker/shift/${object.id}`"
	>
	</manage-shift-workers>
</template>
