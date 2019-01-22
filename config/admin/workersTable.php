<?php
return [
	'model' => \App\Models\Worker::class,
	'where' => [['user_type', \App\Models\Worker::class]],
	'joins' => [
		['users', 'users.user_id', 'workers.id'],
	],


	'fields' => [[
		'name' => 'workers.id',
		'title' => 'id',
		'visible' => false,
	], [
		'name' => 'name',
		'table' => 'users',
		'title' => 'global.name',
		'sortField' => 'name',
	], [
		'name' => 'workplacesList',
		'noTable' => true,
		'title' => 'admin/workers.workplaces',
	], [
		'name' => 'completed',
		'noTable' => true,
		'title' => 'admin/workers.completed',
	]],


];
