<?php

namespace App\Http\Requests\Admin\Invoice;

use App\Jobs\SendApplicationInvoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use App\Services\InvoiceService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest {
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
			'tax' => 'required|in:0,21'
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
		$application = $this->invoice->owner;
		$number = $this->invoice->formattedNumber;
		
		if ($this->input('file_download', false)) {
			$invoiceService = new InvoiceService($application);
			$invoice = $invoiceService->generate($number, $this->input('items'), $this->input('tax'), $this->invoice->created_at);
			return $invoice->download($number);
		}
		$this->invoice->items()->delete();
		$this->invoice->tax = $this->input('tax');
		$total = 0;
		foreach ($this->input('items') as $item) {
			$invoiceItem = new InvoiceItem;
			$invoiceItem->quantity = $item['quantity'];
			$invoiceItem->name = $item['item'];
			$invoiceItem->unit_price = $item['unitPrice'];
			
			if ($service = Service::where("name_en", $item['item'])->orWhere("name_nl", $item['item'])->first()) {
				$invoiceItem->service_id = $service->id;
			}
			$this->invoice->items()->save($invoiceItem);
			if ($invoiceItem->service_id) {
				$application->registerNewServices($service);
			}
			$total += $item['quantity'] * $item['unitPrice'];
		}
		
		$this->invoice->amount = $total;
		$this->invoice->save();
		SendApplicationInvoice::dispatch($this->invoice, $this->input('recipient'), $this->input('subject'), $this->input('message'), $this->input('attachments', []), collect([
			$this->input('bcc', false),
			$this->input('accountant', false) ? app('settings')->get('invoices_accountant') : false
		])->filter());
		
		return $this->invoice->load('payments');
	}
}
