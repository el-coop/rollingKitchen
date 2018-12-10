<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\PDF\DeletePDFRequest;
use App\Http\Requests\Admin\PDF\UpdatePDFRequest;
use App\Http\Requests\Admin\PDF\UploadPDFRequest;
use App\Models\Pdf;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PDFController extends Controller {
	
	public function index() {
		$pdfs = Pdf::all();
		return view('admin.filesystem.show', compact('pdfs'));
	}
	
	public function upload(UploadPDFRequest $request) {
		return $request->commit();
	}
	
	public function update(UpdatePDFRequest $request, Pdf $pdf) {
		return $request->commit();
	}
	
	public function destroy(DeletePDFRequest $request, Pdf $pdf) {
		$request->commit();
		return ['success' => true];
	}
}
