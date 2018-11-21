<?php


Route::group(['prefix' => 'kitchen', 'namespace' => 'Kitchen'], function () {
	
	Route::group(['prefix' => 'applications/{application}/products'], function () {
		Route::post('/', 'ApplicationProductController@create');
		Route::patch('/{product}', 'ApplicationProductController@update');
		Route::delete('/{product}', 'ApplicationProductController@destroy');
	});
});
