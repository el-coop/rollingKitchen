<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Worker\DisapproveWorkersRequest;
use App\Http\Requests\Admin\Worker\SendUpdateInformationRequest;
use App\Http\Requests\Admin\Worker\StoreTaxReviewRequest;
use App\Http\Requests\Admin\Worker\CreateWorkerRequest;
use App\Http\Requests\Admin\Worker\DestroyWorkerRequest;
use App\Http\Requests\Admin\Worker\UpdateWorkerRequest;
use App\Models\Field;
use App\Models\TaxReview;
use App\Models\Worker;
use App\Http\Controllers\Controller;
use App\Services\WorkedHoursService;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use View;

class WorkerController extends Controller {
    public function index() {
        $title = __('admin/workers.workers');
        $createTitle = __('admin/workers.createWorker');
        $fieldType = "Worker";
        $formattersData = collect([
            'totalDataCount' => Field::where('form', Worker::class)->count()
        ]);

        $buttons = [
            view('admin.workers.disapproveAllButton')
        ];
        $extraSlotView = 'admin.workers.updateInformationButton';

        return view('admin.datatableWithNew', compact('title', 'createTitle', 'buttons', 'fieldType', 'formattersData', 'extraSlotView'));

    }

    public function disapprove(DisapproveWorkersRequest $request) {

        $request->commit();
        return back()->with('toast', [

            'type' => 'success',
            'title' => __('vue.updateSuccess'),
            'message' => ' '
        ]);

    }

    public function pdf(Request $request, Worker $worker, WorkedHoursService $workedHoursService) {
        $workerData = $workedHoursService->individual($worker);
        $shifts = $worker->shifts()
            ->where('closed', true)
            ->where('date', '>', $request->input('date', 0))
            ->with('workplace.workFunctions')
            ->orderBy('date', 'desc')->get();

        $images = $worker->photos->map(function ($photo) {
            $encryptedContents = \Storage::get("public/photos/{$photo->file}");
            $decryptedContents = base64_encode(\Crypt::decrypt($encryptedContents));

            return "data:image/jpg;base64,{$decryptedContents}";
        });
        $options = new Options();

        $options->set('isRemoteEnabled', true);
        $pdf = new Dompdf($options);
        $pdf->loadHtml(View::make('admin.workers.pdf', compact('worker', 'shifts', 'workerData', 'images')));
        $pdf->render();

        return $pdf->stream("{$worker->user->name}.pdf");
    }

    public function create() {
        return (new Worker)->fullData;
    }

    public function show(Worker $worker) {
        $worker->load('photos', 'user', 'taxReviews');
        $indexLink = action('Admin\WorkerController@index', [], false);

        $futureShifts = $worker->shifts()->where('date', '>', Carbon::yesterday())->with('workplace.workFunctions')->orderBy('date')->get();

        $workerFields = Worker::fields();

        $leftSideFields = $workerFields->filter(function ($field) {
            return $field->status !== 'protected';
        })->pluck('name');

        $rightSideFields = $workerFields->filter(function ($field) {
            return $field->status === 'protected';
        })->pluck('name');

        $pastShifts = $worker->shifts()->where('date', '<=', Carbon::yesterday())->with('workplace.workFunctions')->orderBy('date')->get();

        $totalHours = new Carbon('today');
        $startOfDay = $totalHours->clone();
        $pastShifts->each(function ($shift) use ($totalHours) {
            $totalHours->add($shift->pivot->workedHours);
        });
        $totalHours = $startOfDay->diffAsCarbonInterval($totalHours);

        return view('admin.workers.show', compact('worker', 'totalHours', 'futureShifts', 'pastShifts', 'indexLink', 'leftSideFields', 'rightSideFields'));
    }

    public function store(CreateWorkerRequest $request) {
        return $request->commit();
    }

    public function edit(Worker $worker) {
        return $worker->fullData->map(function ($item){
            if ($item['name'] == 'liability'){
                $item['disabled'] = true;
            }
            return $item;
        });

    }

    public function update(UpdateWorkerRequest $request, Worker $worker) {
        return $request->commit();
    }

    public function nonAjaxUpdate(UpdateWorkerRequest $request, Worker $worker) {
        $request->commit();
        return redirect()->back()->with('toast', [
            'type' => 'success',
            'title' => '',
            'message' => __('vue.updateSuccess')
        ]);
    }

    public function destroy(DestroyWorkerRequest $request, Worker $worker) {
        $request->commit();

        return [
            'success' => true
        ];
    }

    public function storeTaxReview(StoreTaxReviewRequest $request, Worker $worker) {
        return $request->commit();
    }

    public function destroyTaxReview(Worker $worker, TaxReview $taxReview) {
        $taxReview->delete();
        return [
            'success' => true
        ];
    }

    public function sendUpdateInformationEmail(SendUpdateInformationRequest $request, Worker $worker) {
        return $request->commit();
    }
}
