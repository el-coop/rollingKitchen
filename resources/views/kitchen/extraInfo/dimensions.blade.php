@component('kitchen.extraInfo.yesNo', [
    'application' => $application,
    'name' => 'outside_serving',
    'label' => __('kitchen/extraInfo.outsideServing'),
    'readonly' => $readonly ?? false,
])
    <dynamic-fields class="mb-5" :fields="{{ collect([[
        'name' => 'outside_serving_depth',
        'label' => __('kitchen/extraInfo.outsideServingDepth'),
        'value' => ($readonly ?? false) ? $application->outside_serving_depth : old('outside_serving_depth', $application->outside_serving_depth),
        'readonly' => ($readonly ?? false) || !$application->isExtraInfoOpen(),
        'type' => 'text',
        'subType' => 'number',
        'step' => 0.1,
        'placeholder' => __('kitchen/dimensions.inMeters'),
        'error' => $errors->has('outside_serving_depth') ? $errors->get('outside_serving_depth') : null,
    ]]) }}">
    </dynamic-fields>
@endcomponent
<dynamic-fields :fields="{{ collect([[
    'name' => 'path_distance',
    'label' => __('kitchen/extraInfo.pathDistance'),
    'value' => ($readonly ?? false) ? $application->path_distance : old('path_distance', $application->path_distance),
    'readonly' => ($readonly ?? false) || !$application->isExtraInfoOpen(),
    'type' => 'text',
    'subType' => 'number',
    'step' => 0.1,
    'placeholder' => __('kitchen/dimensions.inMeters'),
    'error' => $errors->has('path_distance') ? $errors->get('path_distance') : null,
]]) }}">
</dynamic-fields>
<p class="help mb-5">@lang('kitchen/extraInfo.pathDistanceHelp')</p>
@component('kitchen.extraInfo.yesNo', [
    'application' => $application,
    'name' => 'own_terrace_furniture',
    'label' => __('kitchen/extraInfo.ownTerraceFurniture'),
    'readonly' => $readonly ?? false,
])
@endcomponent
@component('kitchen.extraInfo.yesNo', [
    'application' => $application,
    'name' => 'needs_backstage',
    'label' => __('kitchen/extraInfo.needsBackstage'),
    'readonly' => $readonly ?? false,
])
    @component('kitchen.extraInfo.yesNo', [
        'application' => $application,
        'name' => 'cooks_in_backstage',
        'label' => __('kitchen/extraInfo.cooksInBackstage'),
        'readonly' => $readonly ?? false,
    ])
    @endcomponent
@endcomponent
