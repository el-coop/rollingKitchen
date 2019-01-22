<?php


Route::group(['prefix' => 'worker', 'namespace' => 'Worker'], function () {
	
	route::group(['middleware' => 'guest', 'prefix' => 'setPassword'], function () {
		Route::get('/{token}', 'WorkerController@showResetForm');
		Route::post('', 'WorkerController@reset');
	});
	
	route::group(['middleware' => ['auth', 'can:view,worker']], function () {
		Route::get('/{worker}', 'WorkerController@index');
		Route::patch('/{worker}', 'WorkerController@update');
		
		Route::post('/{worker}/photo', 'WorkerController@storePhoto');
		Route::delete('/{worker}/photo/{photo}', 'WorkerController@destroyPhoto');
	});
	
});
