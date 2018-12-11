<?php

return [
	'model' => \App\Models\Invoice::class,
	'joins' => [
		['applications', 'invoices.application_id', 'applications.id'],
		['users', 'users.user_id', 'applications.kitchen_id'],
	],

	'fields' => [[
		'name' => 'id',
		'table' => 'invoices',
		'visible' => false
	], [
		'name' => 'application_id',
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
		'table' => 'users',
		'title' => 'auth.kitchenName',
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
