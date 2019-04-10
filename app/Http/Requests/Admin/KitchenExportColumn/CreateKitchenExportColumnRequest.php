<?php

namespace App\Http\Requests\Admin\KitchenExportColumn;

use App\Models\KitchenExportColumn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateKitchenExportColumnRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->can('create', KitchenExportColumn::class);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		$columnOptions = array_keys(KitchenExportColumn::options()->toArray());
		return [
			'column' => ['required', 'string', Rule::in($columnOptions)],
			'name' => 'required|string'
		];
	}

	public function commit() {
		$kitchenExportColumn = new KitchenExportColumn;
		$kitchenExportColumn->column = $this->input('column');
		$kitchenExportColumn->name = $this->input('name');
		$kitchenExportColumn->order = KitchenExportColumn::count();
		$kitchenExportColumn->save();
		return [
			'id' => $kitchenExportColumn->id,
			'column' => $this->input('column'),
			'name' => $this->input('name')
		];

	}
}
