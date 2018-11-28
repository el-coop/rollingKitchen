<?php

namespace App\Http\Requests\Admin\Service;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
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
			'name_nl' => 'required|min:2',
			'name_en' => 'required|min:2',
			'category' => 'required|in:safety,electrical,misc',
			'type' => 'required|in:0,1',
			'price' => 'required|numeric',
        ];
    }

	public function commit() {
		$service = $this->route('service');

		$service->name_nl = $this->input('name_nl');
		$service->name_en = $this->input('name_en');
		$service->category = $this->input('category');
		$service->type = $this->input('type');
		$service->price = $this->input('price');


		$service->save();


		return [
			'id' => $service->id,
			'name_nl' => $this->input('name_nl'),
			'name_en' => $this->input('name_en'),
			'category' => $this->input('category'),
			'price' => $this->input('price')
		];
	}
}
