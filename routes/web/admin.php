<?php


Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'userType:' . \App\Models\Admin::class]], function () {
	Route::get('datatable/list', 'DatatableController@list');
	Route::get('datatable/export', 'DatatableController@export');


	Route::group(['namespace' => 'Admin'], function () {
		Route::group(['prefix' => 'kitchens'], function () {
			Route::get('/', 'KitchenController@index');
			Route::get('/fields', 'KitchenController@fields');
            Route::get('/{kitchen}', 'KitchenController@edit');
			Route::patch('/{kitchen}', 'KitchenController@update');
			Route::get('/fields/list', 'KitchenController@getFields');

			Route::group(['prefix' => 'applications'], function (){

            });
		});

		Route::group(['prefix' => 'field'], function () {
			Route::post('/', 'FieldController@create');
			Route::delete('/{field}', 'FieldController@delete');
			Route::patch('/{field}', 'FieldController@edit');
			Route::post('/saveOrder', 'FieldController@saveOrder');
		});
		Route::group(['prefix' => 'applications'], function (){
            Route::get('/', 'ApplicationController@index');
            Route::get('/{application}', 'ApplicationController@edit');
            Route::patch('/{application}', 'ApplicationController@update');
        });
	});
});
