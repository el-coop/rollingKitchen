<?php

return [
	'model' => \App\Models\Service::class,


	'fields' => [[
		'name' => 'id',
		'visible' => false
	], [
		'name' => 'name',
		'title' => 'misc.name',
		'sortField' => 'name',
	], [
		'name' => 'category',
		'title' => 'misc.category',
		'sortField' => 'category',
	], [
		'name' => 'price',
		'title' => 'misc.price',
		'filter' => false,
	]]
];