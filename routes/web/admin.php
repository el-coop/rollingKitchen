<?php


Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
	Route::get('/kitchens', 'KitchenController@index');
	Route::get('/kitchens/list', 'KitchenController@list');
});