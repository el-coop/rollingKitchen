<?php

return [
	'model' => \App\Models\Service::class,


	'fields' => [[
		'name' => 'id',
		'visible' => false
	], [
		'name' => 'name',
		'title' => 'global.name',
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
	]]
];