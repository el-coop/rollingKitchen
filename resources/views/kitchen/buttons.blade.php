<button class="button is-link">
    @lang('global.save')
</button>
<confirmation-submit button-class="is-danger" label="{{__('global.delete')}} {{__('global.account')}}"
                     title="@lang('kitchen/kitchen.deleteConfirmTitle')"
                     subtitle="@lang('kitchen/kitchen.deleteConfirmSubtitle')"
                     yes-text="@lang('global.yes')"
                     no-text="@lang('global.no')" name="_method" value="delete"></confirmation-submit>
