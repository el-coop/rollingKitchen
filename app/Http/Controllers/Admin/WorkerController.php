<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Worker\CreateWorkerRequest;
use App\Models\Worker;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WorkerController extends Controller {
	public function index() {
		$title = __('admin/workers.workers');
		$createTitle = __('admin/workers.createWorker');
		
		return view('admin.datatableWithNew', compact('title', 'createTitle'));
		
	}
	
	public function create() {
		return (new Worker)->fullData;
	}
	
	public function store(CreateWorkerRequest $request) {
		return $request->commit();
	}
}
