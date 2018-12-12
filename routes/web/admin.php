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
			Route::group(['prefix' => 'payments'], function () {
				Route::post('/{invoice}', 'ApplicationInvoiceController@addPayment');
				Route::get('/{invoice}', 'ApplicationInvoiceController@getPayments');
				Route::delete('/{invoice}/{invoicePayment}', 'ApplicationInvoiceController@destroyPayment');
				Route::patch('/{invoice}/{invoicePayment}', 'ApplicationInvoiceController@updatePayment');
			});
			Route::get('/', 'ApplicationInvoiceController@index');
			Route::get('/{application}', 'ApplicationInvoiceController@create');
			Route::post('/{application}', 'ApplicationInvoiceController@store');
			
			Route::get('/application/{invoice}', 'ApplicationInvoiceController@edit');
			Route::patch('/application/{invoice}', 'ApplicationInvoiceController@update');
			Route::get('/debtor/{invoice}', 'DebtorInvoiceController@edit');
			Route::patch('/debtor/{invoice}', 'DebtorInvoiceController@update');
			
			
			Route::get('/{application}/{invoice}', 'ApplicationInvoiceController@edit');
			Route::patch('/{application}/{invoice}', 'ApplicationInvoiceController@update');
			
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
			Route::group(['prefix' => 'invoice'], function () {
				Route::get('/{debtor}', 'DebtorInvoiceController@create');
				Route::post('/{debtor}', 'DebtorInvoiceController@store');
				Route::get('/{debtor}/{invoice}', 'DebtorInvoiceController@edit');
				Route::patch('/{debtor}/{invoice}', 'DebtorInvoiceController@update');
			});
			
			Route::delete('/delete/{debtor}', 'DebtorController@destroy');
			Route::get('/{debtor}', 'DebtorController@show');
		});
	});
});
