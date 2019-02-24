<?php

namespace App\Http\Requests\Admin\Shift;

use App\Models\Shift;
use Illuminate\Foundation\Http\FormRequest;

class DeleteAllShiftsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
	public function authorize() {
		return $this->user()->can('deleteAll', Shift::class);
	}

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

	public function commit() {

		Shift::all()->each->delete();
	}
}
