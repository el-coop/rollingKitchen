<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Invoice\GenerateDebtorInvoiceRequest;
use App\Http\Requests\Admin\Invoice\UpdateDebtorInvoiceRequest;
use App\Models\Debtor;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DebtorInvoiceController extends Controller {
	public function create(Debtor $debtor) {
		$invoice = (new Invoice);
		$invoice->owner = $debtor;
		return $invoice->fullData;
	}
	
	public function store(GenerateDebtorInvoiceRequest $request, Debtor $debtor) {
		return $request->commit();
	}
	
	public function edit(Debtor $debtor, Invoice $invoice) {
		return $invoice->fullData;
	}
	
	public function update(UpdateDebtorInvoiceRequest $request, Debtor $debtor, Invoice $invoice) {
		return $request->commit();
	}
}
