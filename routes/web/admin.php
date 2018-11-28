<?php

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'userType:' . \App\Models\Admin::class]], function () {
	Route::get('datatable/list', 'DatatableController@list');
	Route::get('datatable/export', 'DatatableController@export');


	Route::group(['namespace' => 'Admin'], function () {
		Route::group(['prefix' => 'kitchens'], function () {
			Route::get('/', 'KitchenController@index');
			Route::get('/{kitchen}', 'KitchenController@show');
			Route::get('/edit/{kitchen}', 'KitchenController@edit');
			Route::patch('/edit/{kitchen}', 'KitchenController@update');
		});

		Route::group(['prefix' => 'services'], function () {

			Route::get('/', 'ServiceController@index');
			Route::get('/edit', 'ServiceController@create');
			Route::post('/edit', 'ServiceController@store');
			Route::patch('/edit/{service}', 'ServiceController@update');
			Route::get('/edit/{service}', 'ServiceController@edit');
			Route::get('/export', 'ServiceController@export');


		});

		Route::group(['prefix' => 'field'], function () {
			Route::post('/', 'FieldController@create');
			Route::get('/{type}', 'FieldController@index');
			Route::delete('/{field}', 'FieldController@destroy');
			Route::patch('/order', 'FieldController@saveOrder');
			Route::patch('/{field}', 'FieldController@edit');
		});
		Route::group(['prefix' => 'applications'], function () {
			Route::get('/', 'ApplicationController@index');
			Route::get('/{application}', 'ApplicationController@show');
			Route::get('/edit/{application}', 'ApplicationController@edit');
			Route::patch('/edit/{application}', 'ApplicationController@update');
			Route::patch('/dimensions/{application}', 'ApplicationController@updateDimensions');
		});

		Route::group(['prefix' => 'settings'], function () {
		   Route::get('/', 'SettingsController@show');
		   Route::patch('/', 'SettingsController@update');
        });

		Route::group(['prefix' => 'fielsystem'], function (){
		   Route::get('/', 'PDFController@index');
		   Route::post('/', 'PDFController@upload');
		   Route::delete('/{pdf}', 'PDFController@destroy');
        });
	});
});
