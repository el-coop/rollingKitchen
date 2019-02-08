<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\WorkedHoursExportColumn\CreateWorkedHoursExportColumnRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WorkedHoursExportColumnController extends Controller {

	public function create(CreateWorkedHoursExportColumnRequest $request){
		return $request->commit();
	}
}
