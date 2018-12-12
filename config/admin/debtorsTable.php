<?php

return [
	'model' => \App\Models\Debtor::class,
	
	'fields' => [[
		'name' => 'id',
		'title' => 'id',
		'visible' => false
	], [
		'name' => 'name',
		'title' => 'global.name',
		'sortField' => 'name',
	], [
		'name' => 'email',
		'title' => 'global.email',
		'sortField' => 'email',
	]]
];
