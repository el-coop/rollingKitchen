@extends('layouts.dashboard')

@section('title',$worker->user->name)

@section('content')
	<div class="tile is-ancestor">
		<div class="tile is-parent">
			<div class="tile is-child">
				<dynamic-form :init-fields="{{ $worker->fulldata }}"
							  url="{{ action('Admin\WorkerController@update', $worker) }}"></dynamic-form>
			</div>
		</div>
		<div class="tile is-parent is-vertical">
			<div class="tile is-child">
				<p class="title">
					@lang('global.photos')
				</p>
				<carousel :photos="{{ $worker->photos }}">

				</carousel>
			</div>
		</div>
	</div>
@endsection

