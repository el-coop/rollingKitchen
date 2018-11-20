<?php


Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {
	Route::get('kitchens/list', 'DatatableController@list');



Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'userType:' . \App\Models\Admin::class]], function () {
	Route::get('datatable/list', 'DatatableController@list');
	Route::get('datatable/export', 'DatatableController@export');
	

	Route::group(['namespace' => 'Admin'], function () {
		Route::group(['prefix' => 'kitchens'], function () {
			Route::get('/', 'KitchenController@index');
			Route::get('/{kitchen}', 'KitchenController@edit');
			Route::patch('/{kitchen}', 'KitchenController@update');

			Route::get('/fields/list', 'KitchenController@getFields');

		});

		Route::group(['prefix' => 'services'], function () {

			Route::get('/', 'ServiceController@index');
			Route::get('/{service}', 'ServiceController@edit');
			Route::patch('/{service}', 'ServiceController@update');

		});
		
		Route::group(['prefix' => 'field'], function () {
			Route::post('/', 'FieldController@create');
			Route::get('/{type}', 'FieldController@index');
			Route::delete('/{field}', 'FieldController@delete');
			Route::patch('/{field}', 'FieldController@edit');
			Route::post('/saveOrder', 'FieldController@saveOrder');
		});
		Route::group(['prefix' => 'applications'], function () {
			Route::get('/', 'ApplicationController@index');
			Route::get('/{application}', 'ApplicationController@edit');
			Route::patch('/{application}', 'ApplicationController@update');
		});
	});
});
