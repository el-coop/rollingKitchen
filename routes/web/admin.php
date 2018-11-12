<?php

Route::get('admin/kitchens/list', 'DatatableController@list');

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
	Route::get('/kitchens', 'KitchenController@index');
	Route::get('/kitchen/fields', 'KitchenController@fields');
	Route::get('/kitchen/fields/list', 'KitchenController@getFields');
    Route::post('/field', 'FieldController@create');
    Route::delete('/field/{field}', 'FieldController@delete');
    Route::patch('/field/{field}', 'FieldController@edit');
});