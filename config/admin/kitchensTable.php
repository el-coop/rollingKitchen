<?php

return [
	'model' => \App\Models\User::class,
	'where' => [['user_type', \App\Models\Kitchen::class]],
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
		'title' => 'global.name',
		'sortField' => 'name',
	], [
		'name' => 'email',
		'title' => 'global.email',
		'sortField' => 'email',
	], [
		'name' => 'status',
		'title' => 'global.status',
		'filter' => [
			'new' => 'admin/kitchens.new',
			'motherlist' => 'admin/kitchens.motherlist'
		],
		'callback' => 'translate'
	], [
		'name' => 'count(kitchen_id)',
		'sortField' => 'count(kitchen_id)',
		'title' => 'global.photos',
		'filter' => [
			'yes' => 'global.yes',
			'no' => 'global.no'
		],
		'filterDefinitions' => [
			'yes' => ['>', 0],
			'no' => ['0', 0],
		],
		'callback' => 'numToBoolTag'
	],[
		'name' => '__component:delete-button',
		'title' => 'global.delete'
	]]
];
