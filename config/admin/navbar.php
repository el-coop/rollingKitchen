<?php

return [
	'admin/kitchens.kitchens' => [
		'admin/kitchens.motherlist' => 'Admin\KitchenController@index',
        'admin/kitchens.applications' => 'Admin\ApplicationController@index',
		'admin/services.index' => 'Admin\ServiceController@index',
	],
    'misc.admin' => [
        'misc.settings' => 'Admin\SettingsController@show',
        'misc.filesystem' => 'Admin\PDFController@index'
    ]
];
