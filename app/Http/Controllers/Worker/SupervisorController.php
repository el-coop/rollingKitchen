<?php

namespace App\Http\Controllers\Worker;

use App\Http\Requests\Admin\Workplace\AddWorkFunctionRequest;
use App\Http\Requests\Admin\Workplace\DeleteWorkFunctionRequest;
use App\Http\Requests\Admin\Workplace\UpdateWorkFunctionRequest;
use App\Http\Requests\Admin\Workplace\UpdateWorkplaceRequest;
use App\Http\Requests\Worker\Supervisor\AddWorkerToShiftRequest;
use App\Http\Requests\Worker\Supervisor\CreateShiftRequest;
use App\Http\Requests\Worker\Supervisor\CreateWorkerRequest;
use App\Http\Requests\Worker\Supervisor\RemoveWorkerFromShiftRequest;
use App\Http\Requests\Worker\Supervisor\UpdateWorkerRequest;
use App\Http\Requests\Worker\Supervisor\UpdateWorkerShiftRequest;
use App\Models\Shift;
use App\Models\Worker;
use App\Models\WorkFunction;
use App\Models\Workplace;
use function foo\func;
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

	public function createWorker() {
		return (new Worker)->fullData->reject(function ($value){
			return $value['name'] == 'workplaces' || $value['name'] == 'Supervisor';
		});
	}

	public function storeWorker(CreateWorkerRequest $request, Workplace $workplace) {
		return $request->commit();
	}

	public function editWorker(Workplace $workplace, Worker $worker) {

		return $worker->fullData->reject(function ($value){
			return $value['name'] == 'Supervisor';
		});

	}

	public function updateWorker(UpdateWorkerRequest $request,Workplace $workplace, Worker $worker) {
		return $request->commit();
	}

	public function editShift(Workplace $workplace, Shift $shift) {
		$fields = $shift->fullData->map(function ($value){
			$value['readonly'] = true;
			return $value;
		});
		$shiftWorkers = $shift->workers->map(function ($worker) use ($shift){
			$shift = $worker->shifts->find($shift);
			return [
				'id' => $worker->id,
				'worker' => $worker->id,
				'start-time'  => date('H:i',strtotime($shift->pivot->start_time)),
				'end-time'  => date('H:i',strtotime($shift->pivot->end_time))

			];
		});
		return [
			'shift' => $fields->toArray(),
			'workers' => $workplace->workers()->with('user')->get()->pluck('user.name', 'id'),
			'shiftWorkers' => $shiftWorkers

		];
	}

	public function addWorkerToShift(AddWorkerToShiftRequest $request, Workplace $workplace, Shift $shift){
		return $request->commit();
	}

	public function updateWorkerShift(UpdateWorkerShiftRequest $request, Workplace $workplace, Shift $shift, Worker $worker){
		return $request->commit();
	}

	public function removeWorkerFromShift(RemoveWorkerFromShiftRequest $request, Workplace $workplace, Shift $shift, Worker $worker){
		return $request->commit();
	}
}
