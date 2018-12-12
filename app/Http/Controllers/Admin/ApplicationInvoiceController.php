<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Invoice\AddPaymentRequest;
use App\Http\Requests\Admin\Invoice\DestroyPaymentRequest;
use App\Http\Requests\Admin\Invoice\GenerateInvoiceRequest;
use App\Http\Requests\Admin\Invoice\UpdateInvoiceRequest;
use App\Http\Requests\Admin\Invoice\UpdatePaymentRequest;
use App\Models\Application;
use App\Models\Invoice;
use App\Models\InvoicePayment;
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

	public function store(GenerateInvoiceRequest $request, Application $application) {
		return $request->commit();
	}

	public function edit(Application $application, Invoice $invoice) {
		return $invoice->fullData;
	}

	public function update(UpdateInvoiceRequest $request, Application $application, Invoice $invoice) {
		return $request->commit();
	}

	public function addPayment(AddPaymentRequest $request, Invoice $invoice) {
		return $request->commit();
	}

	public function updatePayment(UpdatePaymentRequest $request, Invoice $invoice, InvoicePayment $invoicePayment) {
		return $request->commit();
	}

	public function destroyPayment(DestroyPaymentRequest $request, Invoice $invoice, InvoicePayment $invoicePayment) {
		$request->commit();
		return [
			'success' => true
		];
	}

	public function getPayments(Invoice $invoice){
		return $invoice->load('payments');
	}
}
