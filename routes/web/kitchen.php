<?php


Route::group(['prefix' => 'kitchen', 'namespace' => 'Kitchen'], function () {
	
	Route::group(['middleware' => ['guest']], function () {
		Route::get('register', 'KitchenController@create');
		Route::post('register', 'KitchenController@store');
	});
	
	Route::group(['middleware' => ['auth', 'can:update,kitchen']], function () {
		
		Route::get('/{kitchen}', 'KitchenController@edit');
		Route::post('/{kitchen}', 'KitchenController@update');
		
		Route::post('/{kitchen}/photo', 'KitchenController@storePhoto');
		
		
		Route::get('/review/{kitchen}', 'KitchenController@show');
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
