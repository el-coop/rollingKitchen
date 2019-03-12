<?php


Route::group(['prefix' => 'worker', 'namespace' => 'Worker'], function () {
	
	Route::group(['middleware' => 'guest', 'prefix' => 'setPassword'], function () {
		Route::get('/{token}', 'WorkerController@showResetForm');
		Route::post('', 'WorkerController@reset');
	});
	
	Route::group(['middleware' => 'auth'], function () {
		Route::group(['middleware' => 'can:view,worker'], function () {
			Route::get('/{worker}', 'WorkerController@index');
			Route::patch('/{worker}', 'WorkerController@update');
			
			Route::post('/{worker}/photo', 'WorkerController@storePhoto');
			Route::delete('/{worker}/photo/{photo}', 'WorkerController@destroyPhoto');
		});
		
		
		Route::group(['middleware' => 'supervisor'], function () {
			Route::delete('{workplace}/delete/{worker}', 'SupervisorController@destroyWorker');
			Route::group(['prefix' => 'workplace'], function () {
				
				Route::get('{workplace}/worker', 'SupervisorController@createWorker');
				Route::post('{workplace}/worker', 'SupervisorController@storeWorker');
				
				Route::get('{workplace}/worker/{worker}', 'SupervisorController@editWorker');
				Route::patch('{workplace}/worker/{worker}', 'SupervisorController@updateWorker');
				
			});
		});
		
		Route::group(['prefix' => 'shift', 'middleware' => ['can:update,shift']], function () {
			Route::get('{shift}', 'SupervisorController@editShift');
			Route::patch('{shift}', 'SupervisorController@closeShift');
			
			Route::post('{shift}/worker', 'SupervisorController@addWorkerToShift');
			Route::delete('{shift}/worker/{worker}', 'SupervisorController@removeWorkerFromShift');
			Route::patch('{shift}/worker/{worker}', 'SupervisorController@updateWorkerShift');
		});
	});
});
