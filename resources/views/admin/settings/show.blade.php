@extends('layouts.dashboard')

@section('title',__('admin/settings.settings'))

@section('content')
	<div class="box">
		<form method="POST" action="{{action('Admin\SettingsController@update')}}">
			@csrf
			@method('PATCH')
			<tabs>
				@foreach($tabs as $label => $settings)
					<tab label="@lang($label)">
						<div class="columns">
							@foreach($settings as $key => $setting)
								@if($loop->first || ($loop->iteration >= (1+$loop->count/2) && $loop->iteration < (2+$loop->count/2)) )
									<div class="column">
										@endif
										@component('admin.settings.componenets.setting', ['name' => $key])
											@switch($key)
												@case('registration_year')
												<input class="input" type="text" name="{{$key}}" readonly
													   value="{{$setting}}">
												@break
												@case('general_registration_status')
												<label class="switch">
													<input type="checkbox"
														   name="{{$key}}" {{$setting ? 'checked' : ''}}>
													<span class="slider"></span>
												</label>
												@break
												@case('invoices_accountant')
												<input type="email" name="{{$key}}" class="input"
													   value="{{$setting}}" required>
												@break
												@default
												<textarea name="{{$key}}" class="textarea"
														  required>{{$setting}}</textarea>
											@endswitch
										@endcomponent
										@if(($loop->iteration >= $loop->count/2) && ($loop->iteration < (1+$loop->count/2)) || $loop->last)
									</div>
								@endif
							@endforeach

						</div>
					</tab>
				@endforeach
			</tabs>
			<div class="field mt-1">
				<div class="control">
					<button class="button is-success">@lang('global.save')</button>
				</div>
			</div>
		</form>
	</div>
	@include('components.errors')
@endsection
