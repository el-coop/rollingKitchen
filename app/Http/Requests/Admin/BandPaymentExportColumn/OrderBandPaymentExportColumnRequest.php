<?php

namespace App\Http\Requests\Admin\BandPaymentExportColumn;

use App\Models\BandPaymentExportColumn;
use Illuminate\Foundation\Http\FormRequest;

class OrderBandPaymentExportColumnRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->can('order', BandPaymentExportColumn::class);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'order' => 'required|array'
		];
	}

	public function commit() {
		$newOrder = $this->input('order');
		for ($i = 1; $i <= count($newOrder); $i++) {
			$BandPaymentColumn = BandPaymentExportColumn::find($newOrder[$i - 1]);
			$BandPaymentColumn->order = $i;
			$BandPaymentColumn->save();
		}
	}
}
