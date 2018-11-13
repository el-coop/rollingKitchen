<?php


Route::group(['prefix' => 'admin'], function () {
	Route::get('kitchens/list', 'DatatableController@list');
	
	
	Route::group(['namespace' => 'Admin'], function () {
		Route::group(['prefix' => 'kitchens'], function () {
			Route::get('/', 'KitchenController@index');
			Route::get('/{kitchen}', 'KitchenController@edit');
			Route::get('/fields', 'KitchenController@fields');
			Route::get('/fields/list', 'KitchenController@getFields');
		});
		
		Route::group(['prefix' => 'field'], function () {
			Route::post('/', 'FieldController@create');
			Route::delete('/{field}', 'FieldController@delete');
			Route::patch('/{field}', 'FieldController@edit');
		});
	});
});