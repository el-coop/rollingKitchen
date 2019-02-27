<?php

return [
	'admin/kitchens.kitchens' => [
		'admin/kitchens.kitchens' => 'Admin\KitchenController@index',
		'admin/applications.applications' => 'Admin\ApplicationController@index',
		'kitchen/kitchen.services' => 'Admin\ServiceController@index',
		'admin/debtors.debtors' => 'Admin\DebtorController@index',
		'admin/invoices.invoices' => 'Admin\ApplicationInvoiceController@index',
	],
	'admin/workers.workers' => [
		'admin/workers.workers' => 'Admin\WorkerController@index',
		'admin/workers.workplaces' => 'Admin\WorkplaceController@index',
		'worker/worker.shifts' => 'Admin\ShiftController@index',
		'admin/settings.workedHours' => 'Admin\WorkedHoursExportColumnController@show'
	],
	'admin/settings.title' => [
		'admin/settings.settings' => 'Admin\SettingsController@show',
		'admin/settings.files' => 'Admin\PDFController@index',
	],
	'admin/artists.artists' => [
		'admin/artists.artistManager' => 'Admin\ArtistManagerController@index',
		'admin/artists.bands' => 'Admin\BandController@index'
	]
];
