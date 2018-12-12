<?php
return [
	'table' => 'failed_jobs',
	'fields' => [
		[
			'name' => 'id',
			'title' => 'id',
			'visible' => false

		],
		[
			'name' => 'connection',
			'title' => 'developer\failedJobs.connection',
		],
		[
			'name' => 'queue',
			'title' => 'developer\failedJobs.queue',
		],
		[
			'name' => 'exception',
			'title' => 'developer\errors.exception',
		],
		[
			'name' => 'payload',
			'title' => 'developer\failedJobs.payload',
		]
	]
];
