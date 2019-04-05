<?php

namespace App\Http\Requests\BandPaymentExportColumn;

use App\Models\BandPaymentExportColumn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBandPaymentExportColumnRequest extends FormRequest {
	protected $bandPaymentColumn;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->bandPaymentColumn = $this->route('bandPaymentExportColumn');
		return $this->user()->can('update',$this->bandPaymentColumn);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		$columnOptions = array_keys(BandPaymentExportColumn::options()->toArray());
		return [
			'column' => ['required', 'string', Rule::in($columnOptions)],
			'name' => 'required|string'
		];
	}
	public function commit(){
		$this->bandPaymentColumn->column = $this->input('column');
		$this->bandPaymentColumn->name = $this->input('name');
		$this->bandPaymentColumn->save();
		return [
			'id' => $this->bandPaymentColumn->id,
			'column' => $this->input('column'),
			'name' => $this->input('name')
		];
	}
}
