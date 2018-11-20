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
		'name' => 'type',
		'title' => 'misc.type',
		'sortField' => 'type',
	], [
		'name' => 'price',
		'title' => 'misc.price',
		'filter' => false,
	]]
];