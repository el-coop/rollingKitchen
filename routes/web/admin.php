<?php

Route::get('admin/kitchens/list', 'DatatableController@list');

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
	Route::get('/kitchens', 'KitchenController@index');
});