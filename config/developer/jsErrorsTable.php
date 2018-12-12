<?php
return [
	'model' => \App\Models\JsError::class,
	'where' => [['errors.error_type', \App\Models\JsError::class]],
	'joins' => [['errors', 'errors.error_id', 'js_errors.id']],
	'joinsOn' => [['users', 'errors.user_id', '=', 'users.id', 'errors.user_id', '!=' , 'null']],
	'fields' => [
		[
			'name' => 'id',
			'table' => 'errors',
			'title' => 'id',
			'visible' => false

		],
		[
			'name' => 'created_at',
			'title' => 'developer\errors.createdAt',
			'table' => 'errors',
			'filter' => false,
		],
		[
			'name' => 'email',
			'table' => 'users',
			'title' => 'global.email',
			'sortField' => 'email'
		],
		[
			'name' => 'page',
			'table' => 'errors',
			'title' => 'developer\errors.page',
			'sortField' => 'page'
		],
		[
			'name' => 'message',
			'table' => 'js_errors',
			'title' => 'developer\errors.message',
			'sortField' => 'message'
		]
	]
];

