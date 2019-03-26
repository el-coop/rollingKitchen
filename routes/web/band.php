<?php

Route::group(['prefix' => 'band', 'namespace' => 'Band'], function () {
	Route::get('/pdf/{pdf}', 'BandController@showPdf');

	Route::group(['middleware' => 'guest', 'prefix' => 'setPassword'], function () {
		Route::get('/{token}', 'BandController@showResetForm');
		Route::post('', 'BandController@reset');
	});
	Route::group(['middleware' => ['auth', 'can:view,band']], function (){
		Route::get('/{band}', 'BandController@show');
		Route::patch('/{band}', 'BandController@update');



		Route::post('/{band}/bandMember', 'BandController@addBandMember');
		Route::patch('/{band}/bandMember/{bandMember}', 'BandController@updateBandMember');
		Route::delete('/{band}/bandMember/{bandMember}', 'BandController@destroyBandMember');

		Route::patch('/{band}/schedule/{bandSchedule}/approve', 'BandController@approveSchedule');
		Route::patch('/{band}/schedule/{bandSchedule}/reject', 'BandController@rejectSchedule');

	});
});