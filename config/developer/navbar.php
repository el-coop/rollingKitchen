<?php

$adminNav = config('admin.navbar');

return array_merge(['developer/errors.errors' => [
	'developer/errors.js_errors' => 'Developer\ErrorController@jsErrors',
	'developer/errors.php_errors' => 'Developer\ErrorController@phpErrors',
	'developer/failedJobs.failedJobs' => 'Developer\FailedJobsController@index',
]], $adminNav);
