<?php

namespace App\Http\Requests\Admin\WorkedHoursExportColumn;

use App\Models\WorkedHoursExportColumn;
use Illuminate\Foundation\Http\FormRequest;

class DestroyWorkedHoursExportColumnRequest extends FormRequest {
	protected $workedHoursColumn;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->workedHoursColumn = $this->route('workedHoursExportColumn');
		return $this->user()->can('delete',$this->workedHoursColumn);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			//
		];
	}

	public function commit(){
		return $this->workedHoursColumn->delete();
	}
}
