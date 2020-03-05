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
		],
        [
            'name' => 'completed',
            'raw' => 'JSON_LENGTH(data) as completed',
            'sortField' => 'completed',
            'filter' => [
                'yes' => 'global.yes',
                'no' => 'global.no'
            ],
            'filterDefinitions' => [
                'yes' => ['=', function () {
                    return Field::where('form', \App\Models\Band::class)->count();
                }],
                'no' => ['<', function () {
                    return Field::where('form', \App\Models\Band::class)->count();
                }],
            ],
            'title' => 'admin/workers.completed',
            'callback' => 'dataCompleted|' . \App\Models\Band::class
        ]
	]
];
