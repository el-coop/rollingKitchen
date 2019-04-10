<?php

namespace App\Http\Requests\Admin\KitchenExportColumn;

use App\Models\KitchenExportColumn;
use Illuminate\Foundation\Http\FormRequest;

class OrderKitchenExportColumnRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->can('order', KitchenExportColumn::class);
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
			$kitchenExportColumn = KitchenExportColumn::find($newOrder[$i - 1]);
			$kitchenExportColumn->order = $i;
			$kitchenExportColumn->save();
		}
	}
}
