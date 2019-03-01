<?php
return [
	'model' => \App\Models\Band::class,
	'where' => [['users.user_type', \App\Models\Band::class]],
	'joins' => [['users', 'users.user_id', 'bands.id']],
	'fields' => [
		[
			'name' => 'id',
			'table' => 'bands',
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
		]
	]
];