<?php


Route::group(['prefix' => 'worker', 'namespace' => 'Worker'], function () {

	route::group(['middleware' => 'guest', 'prefix' => 'setPassword'], function () {
		Route::get('/{token}', 'WorkerController@showResetForm');
		Route::post('', 'WorkerController@reset');
	});

	route::group(['middleware' => 'auth'], function () {
		route::group(['middleware' => 'can:view,worker'], function () {
			Route::get('/{worker}', 'WorkerController@index');
			Route::patch('/{worker}', 'WorkerController@update');

			Route::post('/{worker}/photo', 'WorkerController@storePhoto');
			Route::delete('/{worker}/photo/{photo}', 'WorkerController@destroyPhoto');
		});
		route::group(['middleware' => 'supervisor'], function () {
			Route::post('workplace/{workplace}/workFunctions', 'SupervisorController@addWorkFunction');
			Route::delete('workplace/{workplace}/workFunctions/{workFunction}', 'SupervisorController@destroyWorkFunction');
			Route::patch('workplace/{workplace}/workFunctions/{workFunction}', 'SupervisorController@updateWorkFunction');

			Route::get('workplace/{workplace}/worker', 'SupervisorController@createWorker');
			Route::post('workplace/{workplace}/worker', 'SupervisorController@storeWorker');

			Route::get('workplace/{workplace}/worker/{worker}', 'SupervisorController@editWorker');
			Route::patch('workplace/{workplace}/worker/{worker}', 'SupervisorController@updateWorker');

			Route::get('workplace/{workplace}/shift/{shift}', 'SupervisorController@editShift');
			Route::patch('workplace/{workplace}/shift/{shift}', 'SupervisorController@closeShift');

			Route::post('workplace/{workplace}/shift/{shift}/worker', 'SupervisorController@addWorkerToShift');
			Route::delete('workplace/{workplace}/shift/{shift}/worker/{worker}', 'SupervisorController@removeWorkerFromShift');
			Route::patch('workplace/{workplace}/shift/{shift}/worker/{worker}', 'SupervisorController@updateWorkerShift');

		});
	});
});
