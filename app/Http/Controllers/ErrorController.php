<?php

namespace App\Http\Controllers;

use App\Http\Requests\Developer\ResolveErrorRequest;
use App\Http\Requests\LogErrorRequest;
use App\Models\Error;
use App\Services\DatatableService;
use App\Services\ErrorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ErrorController extends Controller {

	public function storeJsError(LogErrorRequest $request){
		$request->commit();
	}

	public function jsErrors(){
		$title = $title = __('developer/errors.js_errors');
		return view('developer.errorTable', compact('title'));	}

	public function phpErrors(){
		$title = $title = __('developer/errors.php_errors');
		return view('developer.errorTable', compact('title'));
	}

	public function show(Error $error){
		return $error->fullData;
	}

	public function resolve(ResolveErrorRequest $request,Error $error){
		$request->commit();
		return ['deleted' => true];
	}
}
