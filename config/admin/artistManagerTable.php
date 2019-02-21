<?php

return [
	'model' => \App\Models\ArtistManager::class,
	'where' => [['users.user_type', \App\Models\ArtistManager::class]],
	'joins' => [['users', 'users.user_id', 'artist_managers.id']],
	'fields' => [
		[
			'name' => 'id',
			'table' => 'artist_managers',
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