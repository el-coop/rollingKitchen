<yes-no-tooltip-field :field="{{ collect([
    'name' => ($readonly ?? false) ? "{$name}_{$application->id}" : $name,
    'label' => $label,
    'value' => ($readonly ?? false) ? $application->$name : old($name, $application->$name),
    'disabled' => ($readonly ?? false) || !$application->isExtraInfoOpen(),
    'stacked' => true,
    'yes' => __('global.yes'),
    'no' => __('global.no'),
]) }}" :error="{{ $errors->has($name) ? collect($errors->get($name)) : 'null' }}">
    {{ $slot }}
</yes-no-tooltip-field>
