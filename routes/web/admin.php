<?php


Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
	Route::get('/kitchens', 'KitchenController@index');
	Route::get('/kitchens/list', 'KitchenController@list');
    Route::post('/field', 'FieldController@create');
    Route::delete('/field/{field}', 'FieldController@delete');
    Route::patch('/field/{field}', 'FieldController@edit');
});