<?php

namespace App\Http\Requests\Admin\Service;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest {
	protected $service;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->service = $this->route('service');
		return $this->user()->can('update', $this->service);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name_nl' => 'required|min:2',
			'name_en' => 'required|min:2',
			'category' => 'required|in:safety,electrical,misc',
			'type' => 'required|in:0,1',
			'price' => 'required|numeric',
		];
	}
	
	public function commit() {
		
		$this->service->name_nl = $this->input('name_nl');
		$this->service->name_en = $this->input('name_en');
		$this->service->category = $this->input('category');
		$this->service->type = $this->input('type');
		$this->service->price = $this->input('price');
		
		
		$this->service->save();
		
		
		return [
			'id' => $this->service->id,
			'name_nl' => $this->input('name_nl'),
			'name_en' => $this->input('name_en'),
			'category' => $this->input('category'),
			'price' => $this->input('price')
		];
	}
}
