<?php

namespace App\Http\Requests\Admin\Service;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class CreateServiceRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->can('create', Service::class);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name_nl' => 'required|min:2|unique:services',
			'name_en' => 'required|min:2|unique:services',
			'category' => 'required|in:safety,electrical,misc,socket',
			'type' => 'required|in:0,1,2,3',
			'price' => 'required|numeric',
            'conditions' => 'required_if:type,2,3|array'
		];
	}

	public function commit() {
		$service = new Service;

		$service->name_nl = $this->input('name_nl');
		$service->name_en = $this->input('name_en');
		$service->category = $this->input('category');
		$service->type = $this->input('type');
		$service->price = $this->input('price');
        $service->mandatory = $this->has('mandatory');
        if ($this->input('type') == 2 || $this->input('type') == 3){
            $service->conditions = $this->input('conditions');
        }
		$service->save();


		return [
			'id' => $service->id,
			'name_nl' => $this->input('name_nl'),
			'name_en' => $this->input('name_en'),
			'category' => $this->input('category'),
			'price' => $this->input('price'),
            'mandatory' => $this->has('mandatory')
		];
	}
}
