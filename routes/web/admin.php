<?php

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'userType:' . \App\Models\Admin::class]], function () {
	Route::group(['namespace' => 'Admin'], function () {
		Route::group(['prefix' => 'kitchens'], function () {
			Route::get('/', 'KitchenController@index');
			Route::get('/{kitchen}', 'KitchenController@show');
			Route::delete('/delete/{kitchen}', 'KitchenController@destroy');
			Route::get('/edit/{kitchen}', 'KitchenController@edit');
			Route::patch('/edit/{kitchen}', 'KitchenController@update');
		});

		Route::group(['prefix' => 'services'], function () {
			Route::get('/', 'ServiceController@index');
			Route::get('/edit', 'ServiceController@create');
			Route::post('/edit', 'ServiceController@store');
			Route::patch('/edit/{service}', 'ServiceController@update');
			Route::get('/edit/{service}', 'ServiceController@edit');
			Route::get('/export', 'ServiceController@export');
			Route::delete('/delete/{service}', 'ServiceController@destroy');
		});

		Route::group(['prefix' => 'field'], function () {
			Route::post('/', 'FieldController@create');
			Route::get('/{type}', 'FieldController@index');
			Route::delete('/{field}', 'FieldController@destroy');
			Route::patch('/order', 'FieldController@saveOrder');
			Route::patch('/{field}', 'FieldController@edit');
		});

		Route::group(['prefix' => 'invoices'], function () {

			Route::get('/', 'ApplicationInvoiceController@index');

			Route::group(['prefix' => 'payments'], function () {
				Route::post('/{invoice}', 'ApplicationInvoiceController@addPayment');
				Route::get('/{invoice}', 'ApplicationInvoiceController@getPayments');
				Route::delete('/{invoice}/{invoicePayment}', 'ApplicationInvoiceController@destroyPayment');
				Route::patch('/{invoice}/{invoicePayment}', 'ApplicationInvoiceController@updatePayment');
			});

			Route::group(['prefix' => 'application'], function () {
				Route::get('/{application}', 'ApplicationInvoiceController@create');
				Route::post('/{application}', 'ApplicationInvoiceController@store');
				Route::get('/{application}/{invoice}', 'ApplicationInvoiceController@edit');
				Route::patch('/{application}/{invoice}', 'ApplicationInvoiceController@update');
			});

			Route::group(['prefix' => 'debtor'], function () {
				Route::get('/{debtor}', 'DebtorInvoiceController@create');
				Route::post('/{debtor}', 'DebtorInvoiceController@store');
				Route::get('/{debtor}/{invoice}', 'DebtorInvoiceController@edit');
				Route::patch('/{debtor}/{invoice}', 'DebtorInvoiceController@update');
			});

			Route::group(['prefix' => 'deletedinvoiceowner'], function(){
				Route::get('/{deletedinvoiceowner}/{invoice}', 'DeletedInvoiceOwnerController@edit');
				Route::patch('/{deletedinvoiceowner}/{invoice}', 'DeletedInvoiceOwnerController@update');

			});
		});

		Route::group(['prefix' => 'applications'], function () {
			Route::get('/', 'ApplicationController@index');
			Route::get('/{application}', 'ApplicationController@show');
			Route::get('/edit/{application}', 'ApplicationController@edit');
			Route::patch('/edit/{application}', 'ApplicationController@update');
			Route::patch('/dimensions/{application}', 'ApplicationController@updateDimensions');
		});

		Route::group(['prefix' => 'settings'], function () {
			Route::get('/', 'SettingsController@show');
			Route::patch('/', 'SettingsController@update');
		});

		Route::group(['prefix' => 'fielsystem'], function () {
			Route::get('/', 'PDFController@index');
			Route::post('/', 'PDFController@upload');
			Route::patch('/{pdf}', 'PDFController@update');
			Route::delete('/{pdf}', 'PDFController@destroy');
		});

		Route::group(['prefix' => 'debtors'], function () {
			Route::get('/', 'DebtorController@index');

			Route::group(['prefix' => 'edit'], function () {
				Route::get('/', 'DebtorController@create');
				Route::post('/', 'DebtorController@store');
				Route::get('/{debtor}', 'DebtorController@edit');
				Route::patch('/{debtor}', 'DebtorController@update');
			});

			Route::delete('/delete/{debtor}', 'DebtorController@destroy');
			Route::get('/{debtor}', 'DebtorController@show');
		});

		Route::group(['prefix' => 'workers'], function (){
			Route::get('/workplaces', 'WorkplaceController@index');

			Route::get('/workplaces/edit', 'WorkplaceController@create');
			Route::post('/workplaces/edit', 'WorkplaceController@store');
			Route::delete('/workplaces/delete/{workplace}', 'WorkplaceController@destroy');

			Route::get('/workplaces/edit/{workplace}', 'WorkplaceController@edit');
			Route::patch('/workplaces/edit/{workplace}', 'WorkplaceController@update');

			Route::post('/workplaces/{workplace}', 'WorkplaceController@addWorkFunction');
			Route::delete('/workplaces/{workplace}/{workFunction}', 'WorkplaceController@destroyWorkFunction');
			Route::patch('/workplaces/{workplace}/{workFunction}', 'WorkplaceController@updateWorkFunction');

		});
	});
});
