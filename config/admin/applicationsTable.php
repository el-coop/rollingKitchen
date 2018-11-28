<?php
return [
    'model' => \App\Models\Application::class,
    'where' => [['users.user_type', \App\Models\Kitchen::class],['status','!=','new']],
    'joins' => [['users', 'users.user_id', 'applications.kitchen_id']],
    'fields' => [
        [
            'name' => 'id',
            'table' => 'applications',
            'title' => 'id',
            'visible' => false

        ],
        [
            'name' => 'name',
            'table' => 'users',
            'title' => 'global.name',
            'sortField' => 'name'
        ],
        [
            'name' => 'year',
            'title' => 'global.year',
            'sortField' => 'year'
        ],
        [
            'name' => 'status',
            'title' => 'global.status',
            'sortField' => 'status',
			'filter' => [
				'pending' => 'vue.pending',
				'accepted' => 'vue.accepted',
				'reopened' => 'vue.reopened',
				'rejected' => 'vue.rejected',
				'backup' => 'vue.backup'
			],
			'callback' => 'translate'
        ],
    ]

];
