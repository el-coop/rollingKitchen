<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StageController extends Controller {
	
	public function stage() {
		return view('admin.datatableWithNew');
	}
}
