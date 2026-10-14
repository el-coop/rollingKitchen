<dynamic-fields :fields="{{ collect([[
    'name' => 'other_details',
    'label' => __('kitchen/extraInfo.otherDetails'),
    'value' => ($readonly ?? false) ? $application->other_details : old('other_details', $application->other_details),
    'readonly' => ($readonly ?? false) || !$application->isExtraInfoOpen(),
    'type' => 'textarea',
    'error' => $errors->has('other_details') ? $errors->get('other_details') : null,
]]) }}">
</dynamic-fields>
@if($application->extra_info_submitted_at)
    <p class="mt-2">
        @lang('kitchen/extraInfo.submittedAt', ['date' => $application->extra_info_submitted_at->format('d/m/Y H:i')])
    </p>
@endif
@if(!($readonly ?? false) && $application->isExtraInfoOpen())
    <div class="buttons has-content-justified-center mt-3">
        <confirmation-submit label="@lang('kitchen/extraInfo.submit')"
                             title="@lang('kitchen/extraInfo.submitConfirmTitle')"
                             subtitle="@lang('kitchen/extraInfo.submitConfirmSubtitle')" yes-text="@lang('global.yes')"
                             no-text="@lang('global.no')" name="review" value="1"
                             id="reviewExtraButton"></confirmation-submit>
    </div>
@endif
