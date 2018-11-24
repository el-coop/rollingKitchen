<?php

namespace App\Http\Requests\Kitchen;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDimensionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
    	$this->application = $this->route('application');
        return $this->user()->can('update',$this->application);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
			'length' => 'required|numeric',
			'width' => 'required|numeric',
			'terrace_length' => 'numeric|nullable',
			'terrace_width' => 'numeric|nullable',
			'seats' => 'numeric|nullable'
        ];
    }

	public function commit() {
		$this->application->length = $this->input('length');
		$this->application->width = $this->input('width');
		$this->application->terrace_length = $this->input('terrace_length');
		$this->application->terrace_width = $this->input('terrace_width');
		$this->application->seats = $this->input('seats');
		$this->application->save();
    }
}
