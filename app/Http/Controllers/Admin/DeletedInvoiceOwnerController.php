<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Invoice\UpdateDebtorInvoiceRequest;
use App\Models\DeletedInvoiceOwner;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeletedInvoiceOwnerController extends Controller {
	public function edit(DeletedInvoiceOwner $deletedinvoiceowner, Invoice $invoice) {
		return $invoice->fullData;
	}
	public function update(UpdateDebtorInvoiceRequest $request, DeletedInvoiceOwner $deletedinvoiceowner, Invoice $invoice) {
		return $request->commit();
	}
}
