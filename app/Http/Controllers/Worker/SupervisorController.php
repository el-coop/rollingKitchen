<?php

namespace App\Http\Controllers\Worker;

use App\Http\Requests\Admin\Worker\DestroyWorkerRequest;
use App\Http\Requests\Admin\Workplace\AddWorkFunctionRequest;
use App\Http\Requests\Admin\Workplace\DeleteWorkFunctionRequest;
use App\Http\Requests\Admin\Workplace\UpdateWorkFunctionRequest;
use App\Http\Requests\Admin\Workplace\UpdateWorkplaceRequest;
use App\Http\Requests\Worker\Supervisor\AddWorkerToShiftRequest;
use App\Http\Requests\Worker\Supervisor\CloseShiftRequest;
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
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class SupervisorController extends Controller {
	
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
		return (new Worker)->fullData->reject(function ($value) {
			return $value['name'] == 'workplaces' || $value['name'] == 'supervisor';
		});
	}
	
	public function storeWorker(CreateWorkerRequest $request, Workplace $workplace) {
		return $request->commit();
	}
	
	public function destroyWorker(DestroyWorkerRequest $request, Workplace $workplace, Worker $worker) {
		$request->commit();
		return [
			'success' => true
		];
	}
	
	public function editWorker(Workplace $workplace, Worker $worker) {
		
		return $worker->fullData->reject(function ($value) {
			return $value['name'] == 'supervisor';
		});
		
	}
	
	public function updateWorker(UpdateWorkerRequest $request, Workplace $workplace, Worker $worker) {
		return $request->commit();
	}
	
	public function editShift(Shift $shift) {
		$shiftWorkers = $shift->workers->map(function ($worker) use ($shift) {
			$shift = $worker->shifts->find($shift);
			return [
				'id' => $worker->id,
				'worker' => $worker->id,
				'startTime' => date('H:i', strtotime($shift->pivot->start_time)),
				'endTime' => date('H:i', strtotime($shift->pivot->end_time)),
				'workFunction' => $worker->pivot->work_function_id
			
			];
		});
		return [
			'workers' => $shift->workplace->workers()->where('approved', true)->with('user')->get()->pluck('user.name', 'id'),
			'shiftWorkers' => $shiftWorkers,
			'workFunctions' => $shift->workplace->workFunctions->pluck('name', 'id')
		];
	}
	
	public function closeShift(CloseShiftRequest $request, Shift $shift) {
		return $request->commit();
	}
	
	public function addWorkerToShift(AddWorkerToShiftRequest $request, Shift $shift) {
		return $request->commit();
	}
	
	public function updateWorkerShift(UpdateWorkerShiftRequest $request, Shift $shift, Worker $worker) {
		return $request->commit();
	}
	
	public function removeWorkerFromShift(RemoveWorkerFromShiftRequest $request, Shift $shift, Worker $worker) {
		return $request->commit();
	}
}
