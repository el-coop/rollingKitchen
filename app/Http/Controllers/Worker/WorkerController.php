<?php

namespace App\Http\Controllers\Worker;

use App\Http\Requests\Worker\StorePhotoRequest;
use App\Http\Requests\Worker\UpdateWorkerRequest;
use App\Models\Field;
use App\Models\Worker;
use App\Models\WorkerPhoto;
use Auth;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;

class WorkerController extends Controller {
	
	use ResetsPasswords;
	
	public function index(Worker $worker) {
		$formattersData = [
			'totalDataCount' => Field::where('form', Worker::class)->count()
		];
		return view('worker.worker', compact('worker', 'formattersData'));
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
