<?php

namespace App\Http\Requests\Admin\Invoice;

use App\Jobs\SendDebtorInvoice;
use App\Models\InvoiceItem;
use App\Services\InvoiceService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDebtorInvoiceRequest extends FormRequest {
	private $invoice;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->invoice = $this->route('invoice');
		return $this->user()->can('update', $this->invoice);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		$rules = collect([
			'items' => 'required|array|min:1',
			'items.*.*' => 'required',
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
		$this->invoice = $this->route('invoice');
		$debtor = $this->invoice->owner;
		$number = $this->invoice->formattedNumber;
		
		if ($this->input('file_download', false)) {
			$invoiceService = new InvoiceService($debtor);
			$invoice = $invoiceService->generate($number, $this->input('items'), null, $this->invoice->created_at);
			return $invoice->download($number);
		}
		$this->invoice->items()->delete();
		$this->invoice->tax = 0;
		$total = 0;
		foreach ($this->input('items') as $item) {
			$invoiceItem = new InvoiceItem;
			$invoiceItem->quantity = $item['quantity'];
			$invoiceItem->name = $item['item'];
			$invoiceItem->unit_price = $item['unitPrice'];
			$invoiceItem->tax = $item['tax'];
			$this->invoice->items()->save($invoiceItem);
			
			$total += $item['quantity'] * $item['unitPrice'] * (1 + $item['tax'] / 100);
		}
		
		$this->invoice->amount = $total;
		$this->invoice->save();
		
		SendDebtorInvoice::dispatch($this->invoice, $this->input('recipient'), $this->input('subject'), $this->input('message'), collect([
			$this->input('bcc', false),
			$this->input('accountant', false) ? app('settings')->get('invoices_accountant') : false
		])->filter());
		
		return $this->invoice->load('payments');
	}
}
