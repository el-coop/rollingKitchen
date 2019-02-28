<?php

return [
	'model' => \App\Models\Stage::class,
	
	
	'fields' => [[
		'name' => 'id',
		'visible' => false
	], [
		'name' => 'name',
		'title' => 'global.name',
		'sortField' => 'name',
	]]
];
