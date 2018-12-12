<?php

namespace App\Http\Controllers\Developer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class FailedJobsController extends Controller {
	public function index(){
		return view('developer.failedJobsTable');
	}

	public function retry(Request $request){
		$command = 'queue:retry ' . $request->input('job');
		Artisan::call($command);
	}

	public function delete(Request $request){
		DB::table('failed_jobs')->delete($request->input('job'));
		return $failedJobs = DB::table('failed_jobs')->select('*')->get();

	}

	public function edit(){

	}
}
