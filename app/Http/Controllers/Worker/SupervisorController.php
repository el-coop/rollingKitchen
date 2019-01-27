<?php

namespace App\Http\Controllers\Worker;

use App\Http\Requests\Admin\Workplace\AddWorkFunctionRequest;
use App\Http\Requests\Admin\Workplace\DeleteWorkFunctionRequest;
use App\Http\Requests\Admin\Workplace\UpdateWorkFunctionRequest;
use App\Http\Requests\Admin\Workplace\UpdateWorkplaceRequest;
use App\Http\Requests\Worker\Supervisor\CreateWorkerRequest;
use App\Models\WorkFunction;
use App\Models\Workplace;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SupervisorController extends Controller {

	public function editWorkplace(Workplace $workplace) {
		return $workplace->fullData;
	}

	public function updateWorkplace(UpdateWorkplaceRequest $request, Workplace $workplace) {
		return $request->commit();

	}

	public function addWorkFunction(AddWorkFunctionRequest $request, Workplace $workplace) {
		return $request->commit();
	}

	public function destroyWorkFunction(DeleteWorkFunctionRequest $request, Workplace $workplace, WorkFunction $workFunction) {
		$request->commit();
		return ['success' => true];
	}

	public function updateWorkFunction(UpdateWorkFunctionRequest $request, Workplace $workplace, WorkFunction $workFunction) {
		return $request->commit();
	}

	public function storeWorker(CreateWorkerRequest $request, Workplace $workplace) {
		return $request->commit();
	}

}
