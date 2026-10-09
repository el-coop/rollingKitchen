@component('kitchen.extraInfo.yesNo', [
    'application' => $application,
    'name' => 'open_flame',
    'label' => __('kitchen/extraInfo.openFlame'),
    'readonly' => $readonly ?? false,
])
@endcomponent
@component('kitchen.extraInfo.yesNo', [
    'application' => $application,
    'name' => 'uses_gas',
    'label' => __('kitchen/extraInfo.usesGas'),
    'readonly' => $readonly ?? false,
])
@endcomponent
@component('kitchen.extraInfo.yesNo', [
    'application' => $application,
    'name' => 'uses_electric_devices',
    'label' => __('kitchen/extraInfo.usesElectricDevices'),
    'readonly' => $readonly ?? false,
])
    <div class="mb-5">
        <dynamic-table :columns="[{
            name: 'name',
            label: '@lang('kitchen/extraInfo.device')'
        },{
            name: 'watts',
            label: '@lang('kitchen/extraInfo.watts')',
            subType: 'number',
            type: 'text',
            callback: 'localNumber'
        }]" :init-fields="{{ $application->electricDevices }}"
                       @if(!($readonly ?? false) && $application->isExtraInfoOpen()) action="/kitchen/applications/{{$application->id}}/devices" @endif>
            <template #default="{data}">
                <p>@lang('kitchen/extraInfo.devicesHelp')</p>
                <p class="has-text-weight-bold">
                    @lang('kitchen/extraInfo.total'): @{{ data.reduce((total, device) => total + Number(device.watts), 0) }} W
                </p>
            </template>
        </dynamic-table>
    </div>
@endcomponent
@component('kitchen.extraInfo.yesNo', [
    'application' => $application,
    'name' => 'deep_fryer',
    'label' => __('kitchen/extraInfo.deepFryer'),
    'readonly' => $readonly ?? false,
])
@endcomponent
