<?php

namespace App\Http\Controllers\Developer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class FailedJobsController extends Controller {
	public function index() {
		$deleteButton = true;
		return view('developer.failedJobsTable', compact('deleteButton'));
	}

	public function retry($id) {
		Artisan::call('queue:retry', ['id' => $id]);
		return ['success' => true];
	}

	public function delete($id) {
		DB::table('failed_jobs')->delete($id);
		return ['deleted' => true];
	}

	public function show($id) {
		$failedJob = DB::table('failed_jobs')->where('id', $id)->first();
		return collect([
			[
				'name' => 'connection',
				'label' => __('developer\failedJobs.connection'),
				'type' => 'text',
				'value' => $failedJob->connection,
				'readonly' => true
			],
			[
				'name' => 'queue',
				'label' => __('developer\failedJobs.queue'),
				'type' => 'text',
				'value' => $failedJob->queue,
				'readonly' => true
			],
			[
				'name' => 'payload',
				'label' => __('developer\failedJobs.payload'),
				'type' => 'textarea',
				'value' => $failedJob->payload,
				'readonly' => true
			],
			[
				'name' => 'exception',
				'label' => __('developer\errors.exception'),
				'type' => 'textarea',
				'value' => $failedJob->exception,
				'readonly' => true
			]
		]);
	}
}
