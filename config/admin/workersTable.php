<?php
return [
	'model' => \App\Models\Worker::class,
	'where' => [['user_type', \App\Models\Kitchen::class]],
	'joins' => [
		['users', 'users.user_id', 'workers.id'],
	],
	
	
	
	'fields' => [[
		'name' => 'id',
		'title' => 'id',
		'visible' => false
	], [
		'name' => 'name',
		'title' => 'global.name',
		'sortField' => 'name',
	]]
];
