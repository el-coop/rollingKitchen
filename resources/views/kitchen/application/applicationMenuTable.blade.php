@component('components.nonConfigDatatable', [
    'attribute' => 'menuTable',
    'fields' => $menuTable['fields'],
    'url' => "/kitchen/{$application->id}/menu",
    'withoutFilters' => true
])
    @slot('exportButton', false)
    @slot('deleteButton', true)
    @slot('deleteUrl', "/kitchen/applications/{$application->id}/products/menu")
    @slot('buttons')
        <button type="button" class="button is-light" @click="actions.newObjectForm">@lang('vue.add')</button>
    @endslot
    @slot('ref', 'menuTable')
    <template #default="{object, onUpdate}">
        <template v-if="object">
            <div class="title is-7 has-text-centered">
                <span class="is-size-3" v-text="object.name || '@lang('admin/workers.createWorker')'"></span>
            </div>
            <dynamic-form :url="'applications/{{$application->id}}/products/menu' + (object.id ? `/${object.id}` : '')"
                          :on-data-update="onUpdate"
                          :method="object.id ? 'patch' : 'post'"
            ></dynamic-form>
            <image-manager v-if="object.photosJson" ref="productImages"
                           :url="'applications/{{$application->id}}/products/menu/' + object.id + '/photo'" :data="{
			_token: '{{csrf_token()}}'}"
                           @image-deleted="object.photosJson = JSON.stringify(this.$refs.productImages.images);
                            this.$refs.menuTable.updateObject(object)"
                           @image-uploaded="object.photosJson = JSON.stringify(this.$refs.productImages.images);
                            this.$refs.menuTable.updateObject(object)"
                           :init-images="JSON.parse(object.photosJson)"
                           :delete-url="'applications/{{$application->id}}/products/menu/' + object.id + '/photo'"
            >
            </image-manager>
        </template>
    </template>
@endcomponent
