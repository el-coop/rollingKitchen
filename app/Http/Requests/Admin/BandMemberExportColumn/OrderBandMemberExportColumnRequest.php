<?php

namespace App\Http\Requests\Admin\BandMemberExportColumn;

use App\Models\BandMemberExportColumn;
use Illuminate\Foundation\Http\FormRequest;

class OrderBandMemberExportColumnRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->can('order', BandMemberExportColumn::class);
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

	public function commit() {
		$newOrder = $this->input('order');
		for ($i = 1; $i <= count($newOrder); $i++) {
			$bandMemberColumn = BandMemberExportColumn::find($newOrder[$i - 1]);
			$bandMemberColumn->order = $i;
			$bandMemberColumn->save();
		}
	}
}
