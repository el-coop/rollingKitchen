<?php

namespace App\Http\Requests\Kitchen;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$application = $this->route('application');
		return $this->user()->can('update', $application);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required|min:2',
			'price' => 'required|numeric',
		];
	}

	public function commit() {
		$product = $this->route('product');


		$product->name = $this->input('name');
		$product->price = $this->input('price');

		$product->save();

		return $product;
	}
}
