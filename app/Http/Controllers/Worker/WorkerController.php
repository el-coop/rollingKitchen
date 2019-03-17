<?php

namespace App\Http\Controllers\Worker;

use App;
use App\Http\Requests\Worker\StorePhotoRequest;
use App\Http\Requests\Worker\UpdateWorkerRequest;
use App\Models\Field;
use App\Models\Worker;
use App\Models\WorkerPhoto;
use Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;

class WorkerController extends Controller {
	
	use ResetsPasswords;
	
	public function index(Worker $worker) {
		$locale = App::getLocale();
		$formattersData = [
			'totalDataCount' => Worker::fields()->count()
		];
		
		$worker->load('photos', 'taxReviews', 'user');
		
		$futureShifts = $worker->shifts()->where('date', '>', Carbon::yesterday())->with('workplace.workFunctions')->orderBy('date')->get();
		$pastShifts = $worker->shifts()->where('date', '<=', Carbon::yesterday())->with('workplace.workFunctions')->orderBy('date')->get();
		
		$rightSideFields = Worker::fields()->filter(function ($field) {
			return $field->status === 'protected';
		})->map(function ($field) {
			return "worker[{$field->id}]";
		});
		
		$totalHours = new Carbon('today');
		$startOfDay = $totalHours->clone();
		$pastShifts->each(function ($shift) use ($totalHours) {
			$totalHours->add($shift->pivot->workedHours);
		});
		$totalHours = $startOfDay->diffAsCarbonInterval($totalHours);
		$privacyStatement = str_replace(PHP_EOL, '<br>', app('settings')->get("workers_privacy_statement_{$locale}"));
		
		return view('worker.worker', compact('worker', 'futureShifts', 'totalHours', 'pastShifts', 'formattersData', 'rightSideFields', 'privacyStatement'));
	}
	
	public function showResetForm(Request $request, $token = null) {
		return view('worker.setPassword')->with(
			['token' => $token, 'email' => $request->email]
		);
	}
	
	public function update(UpdateWorkerRequest $request, Worker $worker) {
		$request->commit();
		return back()->with('toast', [
			'type' => 'success',
			'title' => '',
			'message' => __('vue.updateSuccess', [], $request->input('language'))
		]);
		
	}
	
	public function storePhoto(Worker $worker, StorePhotoRequest $request) {
		return $request->commit();
	}
	
	public function destroyPhoto(Worker $worker, WorkerPhoto $photo) {
		$photo->delete();
		return [
			'success' => true
		];
	}
	
	public function broker() {
		return Password::broker('workers');
	}
	
	
	public function redirectTo() {
		return Auth::user()->user->homePage();
	}
}
