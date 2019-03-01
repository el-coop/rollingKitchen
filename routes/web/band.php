<?php

Route::group(['prefix' => 'band', 'namespace' => 'Band'], function () {
	Route::group(['middleware' => 'guest', 'prefix' => 'setPassword'], function () {
		Route::get('/{token}', 'BandController@showResetForm');
		Route::post('', 'BandController@reset');
	});
});