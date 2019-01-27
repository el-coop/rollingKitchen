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
    <div class="tile is-parent">
        <div class="tile is-child">
            <dynamic-table :columns="[{
                        name: 'name',
                        label: '@lang('global.name')'
                    },{
                        name: 'email',
                        label: '@lang('global.email')',

                    }, {
                        name: 'language',
                        label: '@lang('global.language')',
					    type: 'select',
                        options: {
						en: '@lang('global.en')',
						nl: '@lang('global.nl')',
					    },
					    translate: true
						},{
						 name: 'type',
						 label: '@lang('admin/workers.type')',
					     type: 'select',
						 options: {
						      0: '@lang('admin/workers.payroll')',
						      1: '@lang('admin/workers.freelance')',
						      2: '@lang('admin/workers.volunteer')'
						    },
						 translate: true
						    }]" :init-fields="{{$workplace->workersForSupervisor}}" class="mb-1"
                           action="{{action('Worker\SupervisorController@storeWorker', $workplace)}}">
            </dynamic-table>
        </div>
    </div>
</div>