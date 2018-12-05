<?php

Route::group(['prefix' => 'developer'], function(){
	Route::post('/error/jsError', 'ErrorController@storeJsError');
	Route::group(['middleware' => ['auth', 'userType:' . \App\Models\Developer::class]], function (){

		Route::get('phpErrors', 'ErrorController@phpErrors');
		Route::get('phpErrors/edit/{error}', 'ErrorController@show');
		Route::delete('phpErrors/edit/{error}', 'ErrorController@resolve');

		Route::get('jsErrors', 'ErrorController@jsErrors');
		Route::get('jsErrors/edit/{error}', 'ErrorController@show');
		Route::delete('jsErrors/edit/{error}', 'ErrorController@resolve');
	});
});
