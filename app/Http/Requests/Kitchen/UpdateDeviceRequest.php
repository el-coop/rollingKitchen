<?php

namespace App\Http\Requests\Kitchen;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeviceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
		$application = $this->route('application');
		return $this->user()->can('update', $application);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
			'name' => 'required|min:2',
			'watts' => 'required|numeric',
        ];
    }
	
	public function commit() {
		$device = $this->route('device');
		
		
		$device->name = $this->input('name');
		$device->watts = $this->input('watts');
		
		$device->save();
		
		return $device;
	}
}
