<?php

Route::group(['prefix' => 'artistManager', 'namespace' => 'ArtistManager'], function () {
	Route::group(['middleware' => 'guest', 'prefix' => 'setPassword'], function () {
		Route::get('/{token}', 'ArtistManagerController@showResetForm');
		Route::post('', 'ArtistManagerController@reset');
	});
});