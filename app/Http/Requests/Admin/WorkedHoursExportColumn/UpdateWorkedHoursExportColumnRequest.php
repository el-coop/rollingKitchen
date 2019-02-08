<?php

namespace App\Http\Requests\Admin\WorkedHoursExportColumn;

use App\Models\WorkedHoursExportColumn;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkedHoursExportColumnRequest extends FormRequest {
	protected $workedHoursColumn;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->workedHoursColumn = $this->route('workedHoursExportColumn');
		return $this->user()->can('update', WorkedHoursExportColumn::class);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'column' => 'required|string|unique:worked_hours_export_columns,column,' . $this->workedHoursColumn->id ,
			'name' => 'required|string|unique:worked_hours_export_columns,name,' . $this->workedHoursColumn->id
		];
	}
	public function commit(){
		$this->workedHoursColumn->column = $this->input('column');
		$this->workedHoursColumn->name = $this->input('name');
		$this->workedHoursColumn->save();
		return [
			'id' => $this->workedHoursColumn->id,
			'column' => $this->input('column'),
			'name' => $this->input('name')
		];
	}
}
