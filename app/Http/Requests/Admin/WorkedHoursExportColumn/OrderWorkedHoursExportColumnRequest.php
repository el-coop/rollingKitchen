<?php

namespace App\Http\Requests\Admin\WorkedHoursExportColumn;

use App\Models\WorkedHoursExportColumn;
use Illuminate\Foundation\Http\FormRequest;

class OrderWorkedHoursExportColumnRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->can('order', WorkedHoursExportColumn::class);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'order' => 'required|array'
		];
	}

	public function commit(){
		$newOrder = $this->input('order');
		for ($i = 1; $i <= count($newOrder); $i++) {
			$workedHourColumn = WorkedHoursExportColumn::find($newOrder[$i - 1]);
			$workedHourColumn->order = $i;
			$workedHourColumn->save();
		}
	}
}
