<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Worker\CreateWorkerRequest;
use App\Http\Requests\Admin\Worker\UpdateWorkerRequest;
use App\Models\Field;
use App\Models\Worker;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Dompdf\Dompdf;
use View;

class WorkerController extends Controller {
	public function index() {
		$title = __('admin/workers.workers');
		$createTitle = __('admin/workers.createWorker');
		$fieldType = "Worker";
		$formattersData = collect([
			'totalDataCount' => Field::where('form', Worker::class)->count()
		]);
		
		return view('admin.datatableWithNew', compact('title', 'createTitle', 'fieldType', 'formattersData'));
		
	}
	
	public function pdf(Worker $worker) {
		$totalHoursWorked = new Carbon('today');
		$startOfDay = $totalHoursWorked->clone();
		$shifts = $worker->shifts()->where('closed', true)->with('workplace.workFunctions')->orderBy('date', 'desc')->get();
		$shifts->each(function ($shift) use ($totalHoursWorked) {
			$totalHoursWorked->add($shift->pivot->workedHours);
		});
		$totalHoursWorked = $startOfDay->diffAsCarbonInterval($totalHoursWorked);
		return view('admin.workers.pdf', compact('worker', 'shifts', 'startOfDay', 'totalHoursWorked'));
		$pdf = new Dompdf();
		$pdf->loadHtml(View::make('admin.workers.pdf', compact('worker', 'shifts')));
		$pdf->render();
		
		return $pdf->stream("{$worker->user->name}.pdf");
	}
	
	public function create() {
		return (new Worker)->fullData;
	}
	
	public function show(Worker $worker) {
		$worker->load('photos', 'user');
		$indexPage = action('Admin\WorkerController@index');
		return view('admin.workers.show', compact('worker', 'indexPage'));
		
	}
	
	public function store(CreateWorkerRequest $request) {
		return $request->commit();
	}
	
	public function edit(Worker $worker) {
		
		return $worker->fullData;
		
	}
	
	public function update(UpdateWorkerRequest $request, Worker $worker) {
		
		return $request->commit();
	}
	
}
