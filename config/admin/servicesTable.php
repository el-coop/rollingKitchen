<?php

return [
    'model' => \App\Models\Service::class,


    'fields' => [[
        'name' => 'id',
        'visible' => false
    ], [
        'name' => 'name_nl',
        'title' => 'admin/fields.name_nl',
        'sortField' => 'name',
    ], [
        'name' => 'name_en',
        'title' => 'admin/fields.name_en',
        'sortField' => 'name',
    ], [
        'name' => 'category',
        'title' => 'admin/services.category',
        'sortField' => 'category',
        'filter' => [
            'safety' => 'vue.safety',
            'electrical' => 'vue.electrical',
            'misc' => 'vue.misc',
        ],
        'callback' => 'translate'
    ], [
        'name' => 'price',
        'title' => 'kitchen/products.price',
        'filter' => false,
        'sortField' => 'price',
        'callback' => 'localNumber'
    ], [
        'name' => 'mandatory',
        'title' => 'admin/services.mandatory',
        'filter' => false,
        'callback' => 'numToBoolTag'
    ], [
        'name' => 'discontinued',
        'title' => 'admin/services.hidden',
        'filter' => [
            'yes' => 'global.yes',
            'no' => 'global.no'
        ],
        'filterDefinitions' => [
            'yes' => ['>', 0],
            'no' => ['=', 0],
        ],
        'callback' => 'numToBoolTag'
    ]]

];
