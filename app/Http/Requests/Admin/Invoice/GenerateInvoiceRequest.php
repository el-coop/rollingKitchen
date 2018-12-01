<?php

namespace App\Http\Requests\Admin\Invoice;

use App\Models\Invoice;
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
		return [
			//
		];
	}
	
	public function commit() {
		dd($this->all());
		if ($this->input('file_download', false)) {
			return $this->preview();
		}
		
	}
	
	protected function preview() {
		$kitchen = $this->route('application')->kitchen;
		$number = Invoice::getNumber();
		$invoice = InvoiceFile::make()
			->number($number)
			->tax(21)
			->notes('Lrem ipsum dolor sit amet, consectetur adipiscing elit.')
			->customer([
				'name' => $kitchen->user->name,
				'phone' => $kitchen->data['Phone Number'],
				'location' => 'Billing Address',
				'zip' => 'Zip Code',
				'city' => $kitchen->data['City'],
			]);
		
		foreach ($this->input('items') as $item) {
			$invoice->addItem($item['item'], $item['price'], $item['amount']);
		}
		return $invoice->download($number);
	}
}
