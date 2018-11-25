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
{{--<div class="box">--}}
{{--<form method="POST" action="{{action('Admin\PDFController@upload')}}" enctype="multipart/form-data">--}}
{{--@csrf--}}
{{--@method('PUT')--}}
{{--<div class="field">--}}
{{--<label class="label">Name</label>--}}
{{--<div class="control">--}}
{{--<input type="text" class="input" name="name">--}}
{{--</div>--}}
{{--</div>--}}

{{--<div class="field">--}}
{{--<div class="control">--}}
{{--<button class="button is-success">@lang('global.edit')</button>--}}
{{--</div>--}}
{{--</div>--}}
{{--</form>--}}
{{--</div>--}}
