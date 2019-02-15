<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Worker\CreateWorkerRequest;
use App\Http\Requests\Admin\Worker\UpdateWorkerRequest;
use App\Models\Field;
use App\Models\TaxReview;
use App\Models\Worker;
use App\Http\Controllers\Controller;
use App\Services\WorkedHoursService;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
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
	
	public function pdf(Worker $worker, WorkedHoursService $workedHoursService) {
		$workerData = $workedHoursService->individual($worker);
		$shifts = $worker->shifts()->where('closed', true)->with('workplace.workFunctions')->orderBy('date', 'desc')->get();
		$options = new Options();
		
		$options->set('isRemoteEnabled', true);
		$pdf = new Dompdf($options);
		$pdf->loadHtml(View::make('admin.workers.pdf', compact('worker', 'shifts', 'workerData')));
		$pdf->render();
		
		return $pdf->stream("{$worker->user->name}.pdf");
	}
	
	public function create() {
		return (new Worker)->fullData;
	}
	
	public function show(Worker $worker) {
		$worker->load('photos', 'user');
		$indexLink = action('Admin\WorkerController@index', [], false);
		
		$futureShifts = $worker->shifts()->where('date', '>', Carbon::yesterday())->with('workplace.workFunctions')->orderBy('date')->get();
		
		
		$pastShifts = $worker->shifts()->where('date', '<=', Carbon::yesterday())->with('workplace.workFunctions')->orderBy('date')->get();
		
		$totalHours = new Carbon('today');
		$startOfDay = $totalHours->clone();
		$pastShifts->each(function ($shift) use ($totalHours) {
			$totalHours->add($shift->pivot->workedHours);
		});
		$totalHours = $startOfDay->diffAsCarbonInterval($totalHours);
		
		return view('admin.workers.show', compact('worker', 'totalHours', 'futureShifts', 'pastShifts', 'indexLink'));
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
	
	public function addTaxReview(Worker $worker) {
	
	}
	
	public function deleteTaxReview(Worker $worker, TaxReview $taxReview) {
	
	}
}
