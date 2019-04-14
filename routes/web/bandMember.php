<?php
Route::group(['prefix' => 'bandMember', 'namespace' => 'BandMember'], function () {
	
	Route::group(['middleware' => 'guest', 'prefix' => 'setPassword'], function () {
		Route::get('/{token}', 'BandMemberController@showResetForm');
		Route::post('', 'BandMemberController@reset');
	});
	Route::group(['prefix' => '{bandMember}', 'middleware' => ['auth', 'can:view,bandMember']], function () {
		Route::get('/', 'BandMemberController@show');
		Route::patch('/', 'BandMemberController@update');
		
		
		Route::post('/photo', 'BandMemberController@storePhoto');
		Route::delete('/photo/{photo}', 'BandMemberController@destroyPhoto');
	});
});
