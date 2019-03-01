@extends('layouts.site')
@section('title', __('admin/artists.bands'))
@section('content')
    <div>
        @component('components.datatableWithNew')
            @slot('customUrl', '\artistManager\bands')
            @slot('createTitle', __('admin/artists.createBand'))
            @slot('withEditLink', false)
        @endcomponent
    </div>
@endsection