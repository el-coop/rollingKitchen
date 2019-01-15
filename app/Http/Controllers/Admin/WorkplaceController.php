<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Workplace\CreateWorkplaceRequest;
use App\Http\Requests\Admin\Workplace\UpdateWorkplaceRequest;
use App\Models\Workplace;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WorkplaceController extends Controller {

	public function index() {
		return view('admin.workplaces.index');
	}

	public function edit(Workplace $workplace){
		return $workplace->fullData;
	}

	public function update(UpdateWorkplaceRequest $request, Workplace $workplace){
		return $request->commit();

	}

	public function create(){
		return (new Workplace)->fullData;
	}

	public function store(CreateWorkplaceRequest $request){
		return $request->commit();
	}
}
