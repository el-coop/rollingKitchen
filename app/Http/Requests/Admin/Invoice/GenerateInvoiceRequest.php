<?php

namespace App\Http\Requests\Admin\Invoice;

use App\Jobs\SendInvoice;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use App\Services\InvoiceService;
use Illuminate\Foundation\Http\FormRequest;

class GenerateInvoiceRequest extends FormRequest {
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
		$application = $this->route('application');
		$number = Invoice::getNumber();
		$prefix = app('settings')->get('registration_year');

		if ($this->input('file_download', false)) {
			$invoiceService = new InvoiceService($application);
			$invoice = $invoiceService->generate("{$prefix}-{$number}", $this->input('tax'), $this->input('items'));
			return $invoice->download("{$prefix}-{$number}");
		}
		$invoice = new Invoice;
		$invoice->prefix = $prefix;
		$invoice->number = $number;
		$invoice->tax = $this->input('tax');
		$application->invoices()->save($invoice);
		$total = 0;
		foreach ($this->input('items') as $item) {
			$invoiceItem = new InvoiceItem;
			$invoiceItem->quantity = $item['quantity'];
			$invoiceItem->name = $item['item'];
			$invoiceItem->unit_price = $item['unitPrice'];
			if ($service = Service::where("name_en", $item['item'])->orWhere("name_nl", $item['item'])->first()) {
				$invoiceItem->service_id = $service->id;
			}

			$invoice->items()->save($invoiceItem);

			if ($invoiceItem->service_id) {
				$application->registerNewServices($service);
			}
			$total += $item['quantity'] * $item['unitPrice'];
		}

		$invoice->amount = $total;
		$invoice->save();

		if (!$this->application->number) {
			$this->application->setNumber();
		}
		SendInvoice::dispatch($invoice, $this->input('recipient'), $this->input('subject'), $this->input('message'), $this->input('attachments', []), collect([
			$this->input('bcc', false),
			$this->input('accountant', false) ? app('settings')->get('invoices_accountant') : false
		])->filter());

		return $invoice->with('payments')->first();
	}
}
