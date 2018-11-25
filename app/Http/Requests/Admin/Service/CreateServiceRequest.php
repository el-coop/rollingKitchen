<?php

namespace App\Http\Requests\Admin\Service;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class CreateServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
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
			'category' => 'required|in:safety,electrical,misc',
			'type' => 'required|in:0,1',
				'price' => 'required|numeric',
        ];
    }

	public function commit() {
		$service = new Service;

		$service->name = $this->input('name');
		$service->category = $this->input('category');
		$service->type = $this->input('type');
		$service->price = $this->input('price');


		$service->save();


		return [
			'id' => $service->id,
			'name' => $this->input('name'),
			'category' => $this->input('category'),
			'price' => $this->input('price')
		];
	}
}
