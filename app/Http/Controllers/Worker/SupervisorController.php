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
use App\Models\ShiftWorker;
use App\Models\Worker;
use App\Models\WorkFunction;
use App\Models\Workplace;
use App\Services\WorkplaceShiftsExportService;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Excel;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class SupervisorController extends Controller {

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
		$shiftWorkers = ShiftWorker::where('shift_id', $shift->id)->get()->map(function ($shiftWorker) {
			return [
				'id' => $shiftWorker->id,
				'worker' => $shiftWorker->worker_id,
				'startTime' => date('H:i', strtotime($shiftWorker->start_time)),
				'endTime' => date('H:i', strtotime($shiftWorker->end_time)),
				'workFunction' => $shiftWorker->work_function_id,
				'hours' => $shiftWorker->WorkedHours->total('hours')
			];
		});
        $workers = $shift->workplace->workers()->with('user')->get();
		return [
			'workers' => $workers->pluck('user.name', 'id')->put(0, ''),
            'workersApproved' => $workers->pluck('approved', 'id'),
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

	public function updateWorkerShift(UpdateWorkerShiftRequest $request, Shift $shift, ShiftWorker $shiftWorker) {
		return $request->commit();
	}

	public function removeWorkerFromShift(RemoveWorkerFromShiftRequest $request, Shift $shift, ShiftWorker $shiftWorker) {
		return $request->commit();
	}

	public function exportShifts(Excel $excel, Request $request, Workplace $workplace) {
		$days = $workplace->shifts()->orderBy('date')->with('workplace', 'shiftWorkers')->get()->filter(function ($day, $index) use ($request) {
			return in_array($index, $request->input('days'));
		});
		return $excel->download(new WorkplaceShiftsExportService($days), "{$workplace->name}.xls");
	}
}
