@extends('layouts.dashboard')

@section('title',__('misc.settings'))

@section('content')
    <div class="box">
        <form method="POST" action="{{action('Admin\SettingsController@update')}}">
            @csrf
            @method('PATCH')
            @foreach($settings as $setting)
                @component('admin.settings.componenets.setting', ['name' => $setting->name])
                    @switch($setting->name)
                        @case('registration_year')
                            <input class="input" type="text" name="registration_year" readonly value="{{$setting->value}}">
                        @break
                        @case('registration_status')
                            <label class="switch">
                                <input type="checkbox" name="registration_status" {{$setting->value ? 'checked' : ''}}>
                                <span class="slider"></span>
                            </label>
                        @break
                        @case('accountant')
                            <input type="email" name="accountant" class="input" value="{{$setting->value}}">
                        @break
                        @default
                        <textarea name="{{$setting->name}}" class="textarea" >{{$setting->value}}</textarea>
                    @endswitch
                @endcomponent
            @endforeach
            <div class="field">
                <div class="control">
                    <button class="button is-success">@lang('global.edit')</button>
                </div>
            </div>
        </form>
    </div>
@endsection
