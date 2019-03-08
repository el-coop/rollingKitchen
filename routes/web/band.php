<?php

Route::group(['prefix' => 'band', 'namespace' => 'Band'], function () {
	Route::group(['middleware' => 'guest', 'prefix' => 'setPassword'], function () {
		Route::get('/{token}', 'BandController@showResetForm');
		Route::post('', 'BandController@reset');
	});
	Route::group(['middleware' => ['auth', 'userType:' . \App\Models\Band::class]], function (){
		Route::get('/{band}', 'BandController@show');
		Route::patch('/{band}', 'BandController@update');
		Route::patch('/paymentMethod/{band}', 'BandController@updatePaymentMethod');

		Route::post('/{band}/bandMember', 'BandController@addBandMember');
		Route::patch('/{band}/bandMember/{bandMember}', 'BandController@updateBandMember');
		Route::delete('/{band}/bandMember/{bandMember}', 'BandController@destroyBandMember');

	});
});