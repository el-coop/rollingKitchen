<?php
return [
	'model' => \App\Models\PhpError::class,
	'where' => [['errors.error_type', \App\Models\PhpError::class]],
	'joins' => [['errors', 'errors.error_id', 'php_errors.id']],
	'joinsOn' => [['users', 'errors.user_id', '=', 'users.id', 'errors.user_id', '!=' , 'null']],
	'fields' => [
		[
			'name' => 'id',
			'table' => 'errors',
			'title' => 'id',
			'visible' => false

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
			'table' => 'php_errors',
			'title' => 'developer\errors.message',
			'sortField' => 'message'
		]
	]
];
