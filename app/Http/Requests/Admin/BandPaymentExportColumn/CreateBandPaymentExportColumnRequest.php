<?php

namespace App\Http\Requests\Admin\BandPaymentExportColumn;

use App\Models\BandPaymentExportColumn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateBandPaymentExportColumnRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->can('create', BandPaymentExportColumn::class);
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
		];
	}

	public function commit() {
		$BandPaymentColumn = new BandPaymentExportColumn;
		$BandPaymentColumn->column = $this->input('column');
		$BandPaymentColumn->order = BandPaymentExportColumn::count();
		$BandPaymentColumn->save();
		return [
			'id' => $BandPaymentColumn->id,
			'column' => $this->input('column'),
		];

	}
}
