<div class="tile is-ancestor">
    <div class="tile is-parent">
        <div class="tile is-child">
            <dynamic-form :init-field="{{$workplace->fullData->except('workFunctions')}}"
                          url="{{action('Worker\SupervisorController@editWorkplace', $workplace)}}"
                          :hide="['workFunctions']"
            >
                <template>
                    <dynamic-table :columns="[{
                        name: 'name',
                        label: '@lang('global.name')'
                    },{
                        name: 'payment_per_hour_before_tax',
                        label: '@lang('admin/workers.payment_per_hour_before_tax')',
                        subType: 'number',
                        callback: 'localNumber',

                    }, {
                        name: 'payment_per_hour_after_tax',
                        label: '@lang('admin/workers.payment_per_hour_after_tax')',
                        subType: 'number',
                        callback: 'localNumber',
                    }]" :init-fields="{{$workplace->workFunctions}}" class="mb-1"
                                   action="{{action('Worker\SupervisorController@addWorkFunction', $workplace)}}">
                    </dynamic-table>
                </template>
            </dynamic-form>
        </div>
    </div>
</div>