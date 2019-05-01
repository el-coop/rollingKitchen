<div v-if="object.pdf">
    <a :href="'{{Request::url() }}/pdf/' + `${object.pdf}`" >@lang('band/band.technicalRequirements')</a>
</div>