<?php

namespace App\Http\Requests\Admin\WorkedHoursExportColumn;

use App\Models\Field;
use App\Models\WorkedHoursExportColumn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateWorkedHoursExportColumnRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->can('create', WorkedHoursExportColumn::class);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		$columnOptions = array_keys(WorkedHoursExportColumn::getOptionsAttribute()->toArray());
		return [
			'column' => ['required', 'string', Rule::in($columnOptions)],
			'name' => 'required|string'
		];
	}

	public function commit() {
		$workedHoursColumn = new WorkedHoursExportColumn;
		$workedHoursColumn->column = $this->input('column');
		$workedHoursColumn->name = $this->input('name');
		$workedHoursColumn->order = WorkedHoursExportColumn::count();
		$workedHoursColumn->save();
		return [
			'id' => $workedHoursColumn->id,
			'column' => $this->input('column'),
			'name' => $this->input('name')
		];

	}
}
