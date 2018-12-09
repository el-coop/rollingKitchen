<?php


Route::group(['prefix' => 'kitchen', 'namespace' => 'Kitchen'], function () {
	
	Route::get('pdf/{pdf}', 'KitchenController@showPdf');
	
	
	Route::group(['middleware' => ['guest', 'kitchenRegistrationOpen']], function () {
		Route::get('register', 'KitchenController@create');
		Route::post('register', 'KitchenController@store');
	});
	
	Route::group(['middleware' => ['auth', 'can:update,kitchen']], function () {
		
		Route::get('/{kitchen}', 'KitchenController@edit');
		Route::patch('/{kitchen}', 'KitchenController@update');
		Route::delete('/{kitchen}', 'KitchenController@destroy');
		
		
		Route::post('/{kitchen}/photo', 'KitchenController@storePhoto');
		Route::delete('/{kitchen}/photo/{photo}', 'KitchenController@destroyPhoto');
	});
	
	Route::group(['prefix' => 'applications/{application}/products', 'middleware' => ['auth', 'can:update,application']], function () {
		Route::post('/', 'ApplicationProductController@create');
		Route::patch('/{product}', 'ApplicationProductController@update');
		Route::delete('/{product}', 'ApplicationProductController@destroy');
	});
	Route::group(['prefix' => 'applications/{application}/devices', 'middleware' => ['auth', 'can:update,application']], function () {
		Route::post('/', 'ApplicationDeviceController@create');
		Route::patch('/{device}', 'ApplicationDeviceController@update');
		Route::delete('/{device}', 'ApplicationDeviceController@destroy');
	});
	
});
