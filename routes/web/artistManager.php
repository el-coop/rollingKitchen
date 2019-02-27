<?php

Route::group(['prefix' => 'artistManager', 'namespace' => 'ArtistManager'], function () {
	Route::group(['middleware' => 'guest', 'prefix' => 'setPassword'], function () {
		Route::get('/{token}', 'ArtistManagerController@showResetForm');
		Route::post('', 'ArtistManagerController@reset');
	});

	Route::group(['prefix' => 'bands', 'middleware' => ['auth', 'userType:' . \App\Models\ArtistManager::class]], function () {
			Route::get('/', 'ArtistManagerController@index');
			Route::get('/edit', 'ArtistManagerController@create');
			Route::post('/edit', 'ArtistManagerController@store');
			Route::get('/edit/{band}', 'ArtistManagerController@edit');
			Route::patch('/edit/{band}', 'ArtistManagerController@update');
			Route::delete('/delete/{band}', 'ArtistManagerController@destroy');
	});
});