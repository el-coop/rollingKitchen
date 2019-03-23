<?php
return [
	'model' => \App\Models\Shift::class,
	'joins' => [
		['workplaces', 'workplaces.id', 'shifts.workplace_id'],
	],
	
	
	'fields' => [[
		'name' => 'shifts.id',
		'title' => 'id',
		'visible' => false,
	], [
		'name' => 'date',
		'title' => 'admin/shifts.date',
		'sortField' => 'date',
		'callback' => 'date'
	], [
		'name' => 'name',
		'title' => 'worker/worker.workplace',
		'sortField' => 'name',
	], [
		'name' => 'hours',
		'title' => 'admin/shifts.hours',
		'sortField' => 'hours',
		'callback' => 'localNumber',
		'filter' => false,
	], [
		'name' => 'closed',
		'title' => 'admin/shifts.closed',
		'callback' => 'boolean',
		'filter' => [
			'1' => 'global.yes',
			'0' => 'global.no'
		]
	]],

];
