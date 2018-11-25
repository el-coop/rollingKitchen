<?php

namespace App\Http\Requests\Kitchen;

use App\Models\ElectricDevice;
use Illuminate\Foundation\Http\FormRequest;

class CreateDeviceRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->application = $this->route('application');
		return $this->user()->can('update', $this->application);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required|min:2',
			'watts' => 'required|numeric',
		];
		
	}
	
	public function commit() {
		$device = new ElectricDevice();
		$device->name = $this->input('name');
		$device->watts = $this->input('watts');
		
		$this->application->electricDevices()->save($device);
		
		return $device;
	}
}
