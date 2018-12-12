<?php

Route::group(['prefix' => 'developer', 'namespace' => 'Developer'], function(){
	Route::post('/error/jsError', 'ErrorController@storeJsError');
	Route::group(['middleware' => ['auth', 'userType:' . \App\Models\Developer::class]], function (){

		Route::get('phpErrors', 'ErrorController@phpErrors');
		Route::get('phpErrors/edit/{error}', 'ErrorController@show');
		Route::delete('phpErrors/delete/{error}', 'ErrorController@resolve');

		Route::get('jsErrors', 'ErrorController@jsErrors');
		Route::get('jsErrors/edit/{error}', 'ErrorController@show');
		Route::delete('jsErrors/delete/{error}', 'ErrorController@resolve');

		Route::get('failedJobs', 'FailedJobsController@index');
		Route::get('failedJobs/show/{id}', 'FailedJobsController@show');

		Route::post('failedJobs/retry/{id}','FailedJobsController@retry' );
		Route::delete('failedJobs/delete/{id}','FailedJobsController@delete' );

	});
});
