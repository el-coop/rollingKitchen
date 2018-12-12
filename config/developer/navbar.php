<?php
return [
	'developer/errors.errors' => [
		'developer/errors.js_errors' => 'Developer/ErrorController@jsErrors',
		'developer/errors.php_errors' => 'Developer/ErrorController@phpErrors',
		'developer/failedJobs.failedJobs' => 'Developer/FailedJobsController@index',
	],
	'admin/kitchens.kitchens' => [
		'admin/kitchens.kitchens' => 'Admin\KitchenController@index',
		'admin/applications.applications' => 'Admin\ApplicationController@index',
		'kitchen/kitchen.services' => 'Admin\ServiceController@index',
		'admin/debtors.debtors' => 'Admin\DebtorController@index',
		'admin/invoices.invoices' => 'Admin\ApplicationInvoiceController@index',
	],
	'admin/settings.title' => [
		'admin/settings.settings' => 'Admin\SettingsController@show',
		'admin/settings.files' => 'Admin\PDFController@index'
	]
];
