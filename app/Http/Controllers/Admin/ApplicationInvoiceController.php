<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Invoice\GenerateInvoiceRequest;
use App\Http\Requests\Admin\Invoice\TogglePaymentStatusRequest;
use App\Http\Requests\Admin\Invoice\UpdateInvoiceRequest;
use App\Models\Application;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApplicationInvoiceController extends Controller {
	
	public function index() {
		$filters = collect([
			'prefix' => app('settings')->get('registration_year')
		]);
		return view('admin.invoiceTable', compact('filters'));
	}
	
	public function create(Application $application) {
		$invoice = (new Invoice);
		$invoice->owner = $application;
		return $invoice->fullData;
	}
	
	public function togglePaid(TogglePaymentStatusRequest $request, Invoice $invoice) {
		return $request->commit();
	}
	
	public function store(GenerateInvoiceRequest $request, Application $application) {
		return $request->commit();
	}
	
	public function edit(Application $application, Invoice $invoice) {
		return $invoice->fullData;
	}
	
	public function update(UpdateInvoiceRequest $request, Application $application, Invoice $invoice) {
		return $request->commit();
	}
}
