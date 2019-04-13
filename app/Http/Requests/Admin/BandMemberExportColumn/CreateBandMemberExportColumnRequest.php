<?php

namespace App\Http\Requests\Admin\BandMemberExportColumn;

use App\Models\BandMemberExportColumn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateBandMemberExportColumnRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->can('create', BandMemberExportColumn::class);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		$columnOptions = array_keys(BandMemberExportColumn::options()->toArray());
		return [
			'column' => ['required', 'string', Rule::in($columnOptions)],
			'name' => 'required|string'
		];
	}

	public function commit() {
		$bandMemberColumn = new BandMemberExportColumn;
		$bandMemberColumn->column = $this->input('column');
		$bandMemberColumn->name = $this->input('name');
		$bandMemberColumn->order = BandMemberExportColumn::count();
		$bandMemberColumn->save();
		return [
			'id' => $bandMemberColumn->id,
			'column' => $this->input('column'),
			'name' => $this->input('name')
		];

	}
}
