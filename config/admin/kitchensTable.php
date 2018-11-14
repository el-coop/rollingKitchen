<?php

return [
	'model' => \App\Models\User::class,
	'where' => ['user_type', \App\Models\Kitchen::class],
	'joins' => [
		['kitchens', 'users.user_id', 'kitchens.id'],
		['photos', 'photos.kitchen_id', 'kitchens.id']
	],
	
	'fields' => [[
		'name' => 'id',
		'table' => 'kitchens',
		'title' => 'id',
		'visible' => false
	], [
		'name' => 'name',
		'title' => 'misc.name',
		'sortField' => 'name',
	], [
		'name' => 'email',
		'title' => 'misc.email',
		'sortField' => 'email',
	], [
		'name' => 'status',
		'title' => 'misc.status',
		'filter' => [
			'new' => 'datatable.new',
			'motherlist' => 'datatable.motherlist'
		],
		'callback' => 'translate'
	], [
		'name' => 'count(kitchen_id)',
		'sortField' => 'count(kitchen_id)',
		'title' => 'misc.photos',
		'filter' => [
			'yes' => 'datatable.yes',
			'no' => 'datatable.no'
		],
		'filterDefinitions' => [
			'yes' => ['>', 0],
			'no' => ['0', 0],
		],
		'callback' => 'numToBoolTag'
	]]
];