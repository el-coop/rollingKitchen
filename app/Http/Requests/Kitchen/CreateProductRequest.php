<?php

namespace App\Http\Requests\Kitchen;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest {
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
			'price' => 'required|numeric',
		];
	}
	
	public function commit() {
		$product = new Product;
		$product->name = $this->input('name');
		$product->price = $this->input('price');
		
		$this->application->products()->save($product);
		
		return $product;
	}
}
