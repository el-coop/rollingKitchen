<?php

namespace App\Http\Requests\Admin\Invoice;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use ConsoleTVs\Invoices\Classes\Invoice as InvoiceFile;

class GenerateInvoiceRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return true;
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
				'bcc' => 'email',
				'message' => 'required|string'
			]);
		}
		return $rules->toArray();
	}
	
	public function commit() {
		$number = Invoice::getNumber();
		$prefix = app('settings')->get('registration_year');
		if ($this->input('file_download', false)) {
			$invoice = $this->generateInvoice($number);
			return $invoice->download("{$prefix}-{$number}");
		}
		$invoice = new Invoice;
		$invoice->prefix = $prefix;
		$invoice->number = $number;
		$invoice->tax = $this->input('tax');
		$application = $this->route('application');
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
			$total += $item['quantity'] * $item['unitPrice'];
		}
		
		$invoice->amount = $total;
		$invoice->save();
		
		
		return $invoice;
	}
	
	protected function generateInvoice($number) {
		$kitchen = $this->route('application')->kitchen;
		$invoice = InvoiceFile::make()
			->logo(asset('/images/logo.png'))
			->number($number)
			->tax($this->input('tax'))
			->notes('Lrem ipsum dolor sit amet, consectetur adipiscing elit.')
			->customer([
				'name' => $kitchen->user->name,
				'phone' => $kitchen->data[5],
				'location' => $kitchen->data[2],
				'zip' => $kitchen->data[3],
				'city' => $kitchen->data[4],
			]);
		
		foreach ($this->input('items') as $item) {
			$invoice->addItem($item['item'], $item['unitPrice'], $item['quantity']);
		}
		return $invoice;
	}
}
