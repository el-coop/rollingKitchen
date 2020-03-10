<?php

Route::group(['prefix' => 'band', 'namespace' => 'Band'], function () {

	Route::group(['middleware' => 'guest', 'prefix' => 'setPassword'], function () {
		Route::get('/{token}', 'BandController@showResetForm');
		Route::post('', 'BandController@reset');
	});
	Route::group(['prefix' => '{band}', 'middleware' => ['auth']], function () {
		Route::group(['middleware' => ['can:view,band']], function () {
			Route::get('/', 'BandController@show');
			Route::patch('/', 'BandController@update');


			Route::post('/bandMember', 'BandController@addBandMember');
			Route::patch('/bandMember/{bandMember}', 'BandController@updateBandMember');
			Route::delete('/bandMember/{bandMember}', 'BandController@destroyBandMember');

			Route::patch('/schedule/{bandSchedule}/approve', 'BandController@approveSchedule');
			Route::patch('/schedule/{bandSchedule}/reject', 'BandController@rejectSchedule');

			Route::patch('/admin/{bandAdmin}', 'BandAdminController@update');
			Route::post('/admin/{bandAdmin}/photo', 'BandAdminController@storePhoto');
			Route::delete('/admin/{bandAdmin}/photo/{photo}', 'BandAdminController@destroyPhoto');
		});

		Route::group(['can:update,band'], function () {
            Route::get('/pdf', 'BandController@showPdf');
            Route::post('/pdf', 'BandController@uploadFile');
            Route::post('/setlist', 'BandController@uploadSetlist');
            Route::get('/setlist', 'BandController@showSetlist');

		});

		Route::group(['middleware' => ['can:manageSongs,band']], function () {

			Route::post('/song', 'SongController@create');
			Route::patch('/song/{song}', 'SongController@update');
			Route::delete('/song/{song}', 'SongController@destroy');
		});
	});
});
