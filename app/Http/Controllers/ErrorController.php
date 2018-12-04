<?php

namespace App\Http\Controllers;

use App\Http\Requests\LogErrorRequest;
use Illuminate\Http\Request;

class ErrorController extends Controller {

	public function storeJsError(LogErrorRequest $request){
		$request->commit();
	}
}
