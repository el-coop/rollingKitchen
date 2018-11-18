<?php
return [
    'model' => \App\Models\Application::class,
    'where' => ['users.user_type', \App\Models\Kitchen::class],
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
            'title' => 'misc.name',
            'sortField' => 'name'
        ],
        [
            'name' => 'year',
            'title' => 'Year',
            'sortField' => 'year'
        ],
        [
            'name' => 'status',
            'title' => 'status',
            'sortField' => 'status'
        ],
    ]

];
