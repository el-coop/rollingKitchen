<?php

namespace App\Http\Requests\Admin\WorkedHoursExportColumn;

use App\Models\Field;
use App\Models\WorkedHoursExportColumn;
use Illuminate\Foundation\Http\FormRequest;

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
		return [
			'column' => 'required|string|unique:worked_hours_export_columns',
			'name' => 'required|string|unique:worked_hours_export_columns'
		];
	}

	public function commit(){
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
