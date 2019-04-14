<div>
	@component('components.nonConfigDatatable', [
	'attribute' => 'bandMembersForDatatable',
	'fields' => $bandMembersForDatatable['fields'],
	'url' => $band->id . '/datatable/bandMemberList',

	])
		@slot('deleteButton', true)
		@slot('buttons')
			<a class="button is-light"
			   href="{{ action('Admin\FieldController@index', 'BandMember') }}">@lang('admin/kitchens.fields')</a>
			<button class="button is-light" @click="actions.newObjectForm">@lang('vue.add')</button>

		@endslot
		<template #default="{object, onUpdate}">
			<template v-if="object">
				<div class="title is-7 has-text-centered">
					<span class="is-size-3"
						  v-text="object.name || '@lang('admin/bandMembers.createBandMembers')'"></span>
				</div>
				<h6 class="subtitle is-6" v-if="object.name">
					<a :href="`/accountant/pdf/bandMember/${object.id}`">download pdf</a>
				</h6>
				<dynamic-form :url="'bandMembers/{{$band->id}}/edit' + (object.id ? `/${object.id}` : '')"
							  :on-data-update="onUpdate"
							  :method="object.id ? 'patch' : 'post'"
				></dynamic-form>

				<carousel v-if="object.name && object.photos.length > 0" class="mt-1" :photos="object.photos">
				</carousel>
			</template>
		</template>
	@endcomponent
</div>
