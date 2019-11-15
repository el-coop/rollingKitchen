<?php

namespace App\Http\Requests\Admin\KitchenExportColumn;

use App\Models\KitchenExportColumn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKitchenExportColumnRequest extends FormRequest {
	protected $kitchenExportColumn;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->kitchenExportColumn = $this->route('kitchenExportColumn');
		return $this->user()->can('update',$this->kitchenExportColumn);
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
		];
	}
	public function commit(){
		$this->kitchenExportColumn->column = $this->input('column');
		$this->kitchenExportColumn->save();
		return [
			'id' => $this->kitchenExportColumn->id,
			'column' => $this->input('column'),
		];
	}
}
