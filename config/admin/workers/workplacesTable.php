<?php
return [
	'model' => \App\Models\Workplace::class,

	'fields' => [[
		'name' => 'id',
		'title' => 'id',
		'visible' => false
	], [
		'name' => 'name',
		'title' => 'global.name',
		'sortField' => 'name',
	]]
];