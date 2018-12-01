<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Invoice\GenerateInvoiceRequest;
use App\Models\Application;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApplicationInvoiceController extends Controller {
	public function edit(Application $application) {
		$invoice = (new Invoice);
		$invoice->application_id = $application->id;
		return $invoice->fullData;
	}
	
	
	public function store(GenerateInvoiceRequest $request, Application $application) {
		return $request->commit();
	}
}
