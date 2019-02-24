<?php

namespace App\Http\Requests\Admin\Worker;

use App\Models\Worker;
use Illuminate\Foundation\Http\FormRequest;

class DisapproveWorkersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
	public function authorize() {
		return $this->user()->can('disapproveAll', Worker::class);
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

		Worker::query()->update(['approved' => false]);
	}
}
