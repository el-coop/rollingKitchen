<?php

namespace App\Http\Requests\Admin\BandMemberExportColumn;

use Illuminate\Foundation\Http\FormRequest;

class DestroyBandMemberExportColumnRequest extends FormRequest
{
	protected $bandMemberColumn;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->bandMemberColumn = $this->route('bandMemberExportColumn');
		return $this->user()->can('delete',$this->bandMemberColumn);
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
		return $this->bandMemberColumn->delete();
	}
}
