<?php

return [
	'model' => \App\Models\Invoice::class,
	'joins' => [['users', 'users.user_id', 'applications.kitchen_id']],
	'joinsOn' => [
		['applications', 'invoices.owner_id', '=', 'applications.id', 'invoices.owner_type', '=', \App\Models\Application::class],
		['debtors', 'invoices.owner_id', '=', 'debtors.id', 'invoices.owner_type', '=', \App\Models\Debtor::class],
	],
	'cases' => [
		"WHEN invoices.owner_type = '" . \App\Models\Debtor::class . "' THEN debtors.name ELSE users.name END as name"
	],
	
	'fields' => [[
		'name' => 'id',
		'table' => 'invoices',
		'visible' => false
	], [
		'name' => 'owner_id',
		'visible' => false
	], [
		'name' => 'number',
		'table' => 'invoices',
		'visible' => false
	], [
		'name' => 'amount',
		'table' => 'invoices',
		'visible' => false
	], [
		'name' => 'tax',
		'table' => 'invoices',
		'visible' => false
	], [
		'name' => 'formattedNumber',
		'noTable' => true,
		'table' => 'invoices',
		'title' => 'admin/invoices.number',
		'filter' => false
	], [
		'name' => 'name',
		'noTable' => true,
		'title' => 'global.name',
		'sortField' => 'name'
	], [
		'name' => 'prefix',
		'title' => 'global.year',
		'sortField' => 'prefix'
	], [
		'name' => 'total',
		'noTable' => true,
		'title' => 'admin/invoices.amount',
		'sortField' => 'amount',
		'callback' => 'localNumber',
		'filter' => false
	], [
		'name' => 'totalPaid',
		'title' => 'admin/invoices.totalPaid',
		'noTable' => true,
		'callback' => 'localNumber'
	]]
];
