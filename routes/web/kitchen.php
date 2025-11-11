<?php


Route::group(['prefix' => 'kitchen', 'namespace' => 'Kitchen'], function () {

    Route::group(['middleware' => 'guest', 'prefix' => 'setPassword'], function () {
        Route::get('/{token}', 'KitchenController@showResetForm');
        Route::post('', 'KitchenController@reset');
    });


	Route::get('pdf/{pdf}', 'KitchenController@showPdf');


	Route::group(['middleware' => ['guest', 'kitchenRegistrationOpen']], function () {
		Route::get('register', 'KitchenController@create');
		Route::post('register', 'KitchenController@store');
	});

	Route::group(['middleware' => ['auth', 'can:update,kitchen']], function () {

		Route::get('/{kitchen}', 'KitchenController@edit');
		Route::patch('/{kitchen}', 'KitchenController@update');
		Route::delete('/{kitchen}', 'KitchenController@destroy');


		Route::post('/{kitchen}/photo', 'KitchenController@storePhoto');
		Route::delete('/{kitchen}/photo/{photo}', 'KitchenController@destroyPhoto');
	});
    Route::group(['prefix' => 'applications/{application}', 'middleware' => ['auth', 'can:update,application']], function (){
        Route::patch('/use-past-application', 'KitchenController@usePastApplication');
        Route::group(['prefix' => 'products'], function () {
            Route::post('/', 'ApplicationProductController@create');
            Route::patch('/{product}', 'ApplicationProductController@update');
            Route::delete('/{product}', 'ApplicationProductController@destroy');
        });
        Route::group(['prefix' => 'devices'], function () {
            Route::post('/', 'ApplicationDeviceController@create');
            Route::patch('/{device}', 'ApplicationDeviceController@update');
            Route::delete('/{device}', 'ApplicationDeviceController@destroy');
        });
        Route::post('/photo', 'KitchenController@storeApplicationSketch');
        Route::delete('/photo/{photo}', 'KitchenController@destroyApplicationSketch');

    });


});
