<h6 v-if="object.pdf" class="subtitle is-5">
    <a :href="'{{Request::url() }}/pdf/' + `${object.pdf}`" >@lang('band/band.technicalRequirements')</a>
</h6>
