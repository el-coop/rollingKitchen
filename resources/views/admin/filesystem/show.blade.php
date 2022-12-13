@extends('layouts.dashboard')

@section('title',__('admin/settings.files'))

@section('content')
    <div class="box">
        <logo-form action="{{action('Admin\LogoController@store')}}"></logo-form>
    </div>
    <div class="box">
        <dynamic-table :columns="[{
            name: 'name',
            label: '@lang('global.name')'
            },{
            name: 'visibility',
            label: '@lang('admin/settings.visibleTo')',
            type: 'select',
            responsiveHidden: true,
            options: [
            '@lang('admin/settings.noKitchens')',
            '@lang('admin/settings.allKitchens')',
            '@lang('admin/settings.acceptedKitchens')',
            '@lang('admin/artists.bands')'
            ],
            callback: 'numerateOptions'
            },{
            name: 'file',
            label: '@lang('admin/settings.chooseFile')',
            type: 'file',
            invisible: true,
            edit: false
            },{
            name: 'default_send_invoice',
            label: '@lang('admin/settings.default_send_invoice')',
            responsiveHidden: true,
            type: 'checkbox',
            hideLabel: true,
            options: [{name: '@lang('admin/settings.default_send_invoice')'}]

            },{
            name: 'default_resend_invoice',
            label: '@lang('admin/settings.default_resend_invoice')',
            responsiveHidden: true,
            type: 'checkbox',
            hideLabel: true,
            options: [{name: '@lang('admin/settings.default_resend_invoice')'}]
            },{
            name: 'terms_and_conditions_nl',
            label: '@lang('admin/settings.terms_and_conditions_nl')',
            responsiveHidden: true,
            type: 'checkbox',
            hideLabel: true,
            options: [{name: '@lang('admin/settings.terms_and_conditions_nl')'}]
            },{
            name: 'terms_and_conditions_en',
            label: '@lang('admin/settings.terms_and_conditions_en')',
            responsiveHidden: true,
            type: 'checkbox',
            hideLabel: true,
            options: [{name: '@lang('admin/settings.terms_and_conditions_en')'}]
            }]"
                       :sortable="true"
                       :init-fields="{{$pdfs}}" action="{{action('Admin\PDFController@upload')}}"
                       :headers="{'Content-Type': 'multipart/form-data'}">
        </dynamic-table>
    </div>
@endsection
<script>
    import LogoForm from "../../../js/Components/Utilities/LogoForm";
    export default {
        components: {LogoForm}
    }
</script>
