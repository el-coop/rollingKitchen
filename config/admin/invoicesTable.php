<?php

return [
	'model' => \App\Models\Invoice::class,
	'joinsOn' => [
		['applications', 'invoices.owner_id', '=', 'applications.id', 'invoices.owner_type', '=', \App\Models\Application::class],
		['debtors', 'invoices.owner_id', '=', 'debtors.id', 'invoices.owner_type', '=', \App\Models\Debtor::class],
		['users', 'applications.kitchen_id', '=', 'users.user_id', 'users.user_type', '=', \App\Models\Kitchen::class],
	],
	'cases' => [
		'WHEN debtors.name IS NULL THEN users.name ELSE debtors.name END as name'
	],
	
	'fields' => [[
		'name' => 'id',
		'table' => 'invoices',
		'visible' => false
	], [
		'name' => 'owner_id',
		'visible' => false
	], [
		'name' => 'owner_type',
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
		'filter' => false,
		'sortField' => 'number',
	
	], [
		'name' => 'name',
		'noTable' => true,
		'title' => 'global.name',
		'sortField' => 'amount',
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
