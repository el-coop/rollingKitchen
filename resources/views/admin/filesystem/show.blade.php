@extends('layouts.dashboard')

@section('title',__('admin/settings.files'))

@section('content')
    <div class="box">
        <dynamic-table :columns="[{
	name: 'name',
	label: '@lang('global.name')'
},{
    name: 'visibility',
    label: '@lang('admin/settings.visibleTo')',
    type: 'select',
    options: [
    	'@lang('admin/settings.noKitchens')',
    	'@lang('admin/settings.allKitchens')',
    	'@lang('admin/settings.acceptedKitchens')',
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
	type: 'checkbox',
	fromDatatable: false,
	options: [{name: '@lang('admin/settings.default_send_invoice')'}]

},{
	name: 'default_resend_invoice',
	label: '@lang('admin/settings.default_resend_invoice')',
	fromDatatable: false,
	type: 'checkbox',
	options: [{name: '@lang('admin/settings.default_resend_invoice')'}]
}]" :init-fields="{{$pdfs}}" action="{{action('Admin\PDFController@upload')}}"
                       :headers="{'Content-Type': 'multipart/form-data'}">
        </dynamic-table>
    </div>
@endsection
