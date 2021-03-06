<?php

namespace App\Http\Requests\Admin\WorkedHoursExportColumn;

use App\Models\WorkedHoursExportColumn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkedHoursExportColumnRequest extends FormRequest {
	protected $workedHoursColumn;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->workedHoursColumn = $this->route('workedHoursExportColumn');
		return $this->user()->can('update',$this->workedHoursColumn);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		$columnOptions = array_keys(WorkedHoursExportColumn::options()->toArray());
		return [
			'column' => ['required', 'string', Rule::in($columnOptions)],
		];
	}
	public function commit(){
		$this->workedHoursColumn->column = $this->input('column');
		$this->workedHoursColumn->save();
		return [
			'id' => $this->workedHoursColumn->id,
			'column' => $this->input('column'),
		];
	}
}
