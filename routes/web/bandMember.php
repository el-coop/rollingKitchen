<?php
Route::group(['prefix' => 'bandMember', 'namespace' => 'BandMember'], function () {

	Route::group(['middleware' => 'guest', 'prefix' => 'setPassword'], function () {
		Route::get('/{token}', 'BandMemberController@showResetForm');
		Route::post('', 'BandMemberController@reset');
	});
});