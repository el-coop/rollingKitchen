<?php

return [
	'table' => 'users',
	'where' => ['user_type', \App\Models\Kitchen::class],
	'joins' => [
		['kitchens', 'users.user_id', 'kitchens.id']
	],
	
	'fields' => [[
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
			'new', 'motherlist'
		]
	]]
];