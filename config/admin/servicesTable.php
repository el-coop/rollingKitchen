<?php

return [
	'model' => \App\Models\Service::class,


	'fields' => [[
		'name' => 'id',
		'visible' => false
	], [
		'name' => 'name_nl',
		'title' => 'misc.nameNl',
		'sortField' => 'name',
	], [
		'name' => 'name_en',
		'title' => 'misc.nameEn',
		'sortField' => 'name',
	],[
		'name' => 'category',
		'title' => 'misc.category',
		'sortField' => 'category',
	], [
		'name' => 'price',
		'title' => 'misc.price',
		'filter' => false,
	]]
];