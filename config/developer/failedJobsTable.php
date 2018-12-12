<?php
return [
	'table' => 'failed_jobs',
	'fields' => [
		[
			'name' => 'id',
			'title' => 'id',
			'visible' => false,
		],
		[
			'name' => 'connection',
			'title' => 'developer\failedJobs.connection',
			'filter' => false
		],
		[
			'name' => 'queue',
			'title' => 'developer\failedJobs.queue',
			'filter' => false
		],
		[
			'name' => 'exception',
			'title' => 'developer\errors.exception',
			'filter' => false
		],
		[
			'name' => 'payload',
			'title' => 'developer\failedJobs.payload',
			'filter' => false
		]
	]
];
