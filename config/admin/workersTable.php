<?php
return [
	'model' => \App\Models\Worker::class,
	'where' => [['user_type', \App\Models\Worker::class]],
	'joins' => [
		['users', 'users.user_id', 'workers.id'],
		['worker_workplace', 'workers.id', 'worker_id'],
		['workplaces', 'worker_workplace.workplace_id', 'workplaces.id'],
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
		'raw' => 'GROUP_CONCAT(workplaces.name, ", ") as workplacesList',
		'filterFields' => ['workplaces.name'],
		'title' => 'admin/workers.workplaces',
	], [
		'name' => 'completed',
		'noTable' => true,
		'title' => 'admin/workers.completed',
	]],


];
