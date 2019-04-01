@extends('layouts.dashboard')

@section('title',__('admin/message.title'))

@section('content')
    <dynamic-form button-text="@lang('admin/invoices.send')" method="post" url="{{action('Admin\BlastMessageController@send')}}" :init-fields="{{$fields}}">

    </dynamic-form>
@endsection
