<?php


Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {
	Route::get('kitchens/list', 'DatatableController@list');


	Route::group(['namespace' => 'Admin'], function () {
		Route::group(['prefix' => 'kitchens'], function () {
			Route::get('/', 'KitchenController@index');
			Route::get('/fields', 'KitchenController@fields')->middleware('can:view,' . \App\Models\Field::class);
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
			Route::post('/', 'FieldController@create')->middleware('can:create,' . \App\Models\Field::class);
			Route::delete('/{field}', 'FieldController@delete')->middleware('can:delete,field');
			Route::patch('/{field}', 'FieldController@edit')->middleware('can:update,field');
			Route::post('/saveOrder', 'FieldController@saveOrder')->middleware('can:view,' . \App\Models\Field::class);
		});
	});
});
