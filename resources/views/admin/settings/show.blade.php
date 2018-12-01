@extends('layouts.dashboard')

@section('title',__('admin/settings.settings'))

@section('content')
    <div class="box">
        <form method="POST" action="{{action('Admin\SettingsController@update')}}">
            @csrf
            @method('PATCH')
            <div class="columns">
                @foreach($settings as $key => $setting)
                    @if($loop->first || ($loop->iteration >= (1+$loop->count/2) && $loop->iteration < (2+$loop->count/2)) )
                        <div class="column">
                            @endif
                            @component('admin.settings.componenets.setting', ['name' => $key])
                                @switch($key)
                                    @case('registration_year')
                                    <input class="input" type="text" name="registration_year" readonly
                                           value="{{$setting}}">
                                    @break
                                    @case('registration_status')
                                    <label class="switch">
                                        <input type="checkbox"
                                               name="registration_status" {{$setting ? 'checked' : ''}}>
                                        <span class="slider"></span>
                                    </label>
                                    @break
                                    @case('accountant')
                                    <input type="email" name="accountant" class="input" value="{{$setting}}">
                                    @break
                                    @default
                                    <textarea name="{{$key}}" class="textarea">{{$setting}}</textarea>
                                @endswitch
                            @endcomponent
                            @if(($loop->iteration >= $loop->count/2) && ($loop->iteration < (1+$loop->count/2)) || $loop->last)
                        </div>
                    @endif
                @endforeach

            </div>
            <div class="field">
                <div class="control">
                    <button class="button is-success">@lang('global.save')</button>
                </div>
            </div>
        </form>
    </div>
@endsection
