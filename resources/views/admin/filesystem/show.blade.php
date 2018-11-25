@extends('layouts.dashboard')

@section('title',__('misc.pdfs'))

@section('content')
    <div class="box">
        <dynamic-table :columns="[{
	name: 'name',
	label: '@lang('global.name')'
},
{
    name: 'file',
    label: '@lang('global.file')',
    type: 'file',
    invisible: true
}
]" :init-fields="{{$pdfs}}" action="{{action('Admin\PDFController@upload')}}" :headers="{'Content-Type': 'multipart/form-data'}" :edit="false">
        </dynamic-table>
    </div>
@endsection
