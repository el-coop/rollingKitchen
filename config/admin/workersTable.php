<?php

use App\Models\Field;

return [
    'model' => \App\Models\Worker::class,
    'where' => [['user_type', \App\Models\Worker::class]],
    'joins' => [
        ['users', 'users.user_id', 'workers.id'],
        ['worker_workplace', 'workers.id', 'worker_workplace.worker_id'],
        ['workplaces', 'worker_workplace.workplace_id', 'workplaces.id'],
        ['worker_photos', 'worker_photos.worker_id', 'workers.id']
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
    ],[
        'name' => 'first_name',
        'title' => 'admin/workers.firstName',
        'sortField' => 'first_name',
    ],[
        'name' => 'surname',
        'title' => 'admin/workers.surname',
        'sortField' => 'first_name',
    ], [
        'name' => 'workplacesList',
        'raw' => 'GROUP_CONCAT(workplaces.name) as workplacesList',
        'filterFields' => ['workplaces.name'],
        'filter' => function () {
            return \App\Models\Workplace::all()->pluck('name', 'name');
        },
        'title' => 'admin/workers.workplaces',
    ], [
        'name' => 'completed',
        'raw' => 'JSON_LENGTH(data) as completed',
        'sortField' => 'completed',
        'filter' => [
            'yes' => 'global.yes',
            'no' => 'global.no'
        ],
        'filterDefinitions' => [
            'yes' => ['=', function () {
                return Field::where('form', \App\Models\Worker::class)->count();
            }],
            'no' => ['<', function () {
                return Field::where('form', \App\Models\Worker::class)->count();
            }],
        ],
        'title' => 'admin/workers.completed',
        'callback' => 'dataCompleted|' . \App\Models\Worker::class
    ], [
        'name' => 'count(file)',
        'sortField' => 'count(file)',
        'title' => 'global.photos',
        'filter' => [
            'yes' => 'global.yes',
            'no' => 'global.no'
        ],
        'filterDefinitions' => [
            'yes' => ['>', 0],
            'no' => ['=', 0],
        ],
        'callback' => 'numToBoolTag'
    ], [
        'name' => 'last_submitted',
        'title' => 'admin/workers.submitted',
        'callback' => 'submitted',
        'filter' => [
            'yes' => 'global.yes',
            'no' => 'global.no'
        ],
        'filterDefinitions' => [
            'yes' => ['=', \Carbon\Carbon::now()->year],
            'no' => ['!=', \Carbon\Carbon::now()->year],
        ],
    ], [
        'name' => 'approved',
        'sortField' => 'approved',
        'title' => 'admin/workers.approved',
        'callback' => 'boolean',
        'filter' => [
            '1' => 'global.yes',
            '0' => 'global.no'
        ]
    ]],


];
