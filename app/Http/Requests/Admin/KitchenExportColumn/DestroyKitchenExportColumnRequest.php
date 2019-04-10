<?php

namespace App\Http\Requests\Admin\KitchenExportColumn;

use Illuminate\Foundation\Http\FormRequest;

class DestroyKitchenExportColumnRequest extends FormRequest {
	protected $kitchenExportColumn;

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->kitchenExportColumn = $this->route('kitchenExportColumn');
		return $this->user()->can('delete', $this->kitchenExportColumn);
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
		return $this->kitchenExportColumn->delete();
	}
}
