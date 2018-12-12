<?php

namespace App\Http\Requests\Admin\Invoice;

use App\Jobs\SendDebtorInvoice;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\InvoiceService;
use Illuminate\Foundation\Http\FormRequest;

class GenerateDebtorInvoiceRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->can('create', Invoice::class);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		$rules = collect([
			'items' => 'required|array|min:1',
			'items.*.*' => 'required'
		]);
		if (!$this->input('file_download', false)) {
			$rules = $rules->merge([
				'recipient' => 'required|email',
				'bcc' => 'nullable|email',
				'message' => 'required|string',
				'subject' => 'required|string',
			]);
		}
		return $rules->toArray();
	}
	
	public function commit() {
		$debtor = $this->route('debtor');
		$number = Invoice::getNumber();
		$prefix = app('settings')->get('registration_year');
		
		if ($this->input('file_download', false)) {
			$invoiceService = new InvoiceService($debtor);
			$invoice = $invoiceService->generate("{$prefix}-{$number}", $this->input('items'));
			return $invoice->download("{$prefix}-{$number}");
		}
		$invoice = new Invoice;
		$invoice->prefix = $prefix;
		$invoice->number = $number;
		$invoice->tax = 0;
		$debtor->invoices()->save($invoice);
		$total = 0;
		foreach ($this->input('items') as $item) {
			$invoiceItem = new InvoiceItem;
			$invoiceItem->quantity = $item['quantity'];
			$invoiceItem->name = $item['item'];
			$invoiceItem->unit_price = $item['unitPrice'];
			$invoiceItem->tax = $item['tax'];
			$invoice->items()->save($invoiceItem);
			
			$total += $item['quantity'] * $item['unitPrice'] * (1 + $item['tax'] / 100);
		}
		
		$invoice->amount = $total;
		$invoice->save();
		
		SendDebtorInvoice::dispatch($invoice, $this->input('recipient'), $this->input('subject'), $this->input('message'), collect([
			$this->input('bcc', false),
			$this->input('accountant', false) ? app('settings')->get('invoices_accountant') : false
		])->filter());
		
		return $invoice->load('payments');
	}
}
