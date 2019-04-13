<?php

namespace App\Http\Requests\Admin\BandMemberExportColumn;

use App\Models\BandMemberExportColumn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBandMemberExportColumnRequest extends FormRequest {
	protected $bandMemberColumn;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->bandMemberColumn = $this->route('bandMemberExportColumn');
		return $this->user()->can('update',$this->bandMemberColumn);
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
	public function commit(){
		$this->bandMemberColumn->column = $this->input('column');
		$this->bandMemberColumn->name = $this->input('name');
		$this->bandMemberColumn->save();
		return [
			'id' => $this->bandMemberColumn->id,
			'column' => $this->input('column'),
			'name' => $this->input('name')
		];
	}
}
