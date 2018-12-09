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
    invisible: true
}]" :init-fields="{{$pdfs}}" action="{{action('Admin\PDFController@upload')}}"
					   :headers="{'Content-Type': 'multipart/form-data'}" :edit="false">
		</dynamic-table>
	</div>
@endsection
