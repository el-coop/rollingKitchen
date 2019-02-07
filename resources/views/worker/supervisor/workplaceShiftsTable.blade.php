@component('components.nonConfigDatatable', [
    'attribute' => 'shiftsForSupervisor',
    'fields' => $shiftsTable['fields'],
    'url' => "/supervisorDatatable/{$workplace->id}"
])
    <template slot-scope="{object, onUpdate}" v-if="object">
        <manage-shift
                :url="'workplace/{{$workplace->id}}/shift' + (object.id ? `/${object.id}` : '')"
                :action="'workplace/{{$workplace->id}}/shift' + (object.id ? `/${object.id}` : '') + '/worker'"
        >

        </manage-shift>
    </template>
@endcomponent
