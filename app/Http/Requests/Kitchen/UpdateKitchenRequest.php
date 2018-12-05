<?php

namespace App\Http\Requests\Kitchen;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKitchenRequest extends FormRequest {
	private $kitchen;
	private $application;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->kitchen = $this->route('kitchen');
		return $this->user()->can('update', $this->kitchen);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		$this->application = $this->kitchen->getCurrentApplication();
		$rules = collect([
			'name' => 'required|min:2',
			'email' => 'required|email',
			'language' => 'required|in:en,nl',
			'kitchen' => 'required|array',
		]);
		
		if ($this->user()->can('update', $this->application)) {
			$rules = $rules->merge([
				'application' => 'required|array',
				'services' => 'array',
				'socket' => 'required|numeric',
				'length' => 'required|numeric',
				'width' => 'required|numeric',
				'terrace_length' => 'numeric|nullable',
				'terrace_width' => 'numeric|nullable',
				'seats' => 'numeric|nullable'
			]);
		}
		
		return $rules->toArray();
	}
	
	public function commit() {
		$this->kitchen->user->name = $this->input('name');
		$this->kitchen->user->email = $this->input('email');
		$this->kitchen->user->language = $this->input('language');
		$this->kitchen->user->save();
		
		
		$this->kitchen->data = $this->input('kitchen');
		$this->kitchen->save();
		
		if ($this->user()->can('update', $this->application)) {
			
			$this->application->data = $this->input('application');
			$this->application->socket = $this->input('socket');
			$this->application->length = $this->input('length');
			$this->application->width = $this->input('width');
			$this->application->terrace_length = $this->input('terrace_length');
			$this->application->terrace_width = $this->input('terrace_width');
			$this->application->seats = $this->input('seats');
			if ($this->input('review')) {
				$this->application->status = 'pending';
				$this->session()->flash('fireworks', true);
			}
			$this->application->save();
			
			$services = collect($this->input('services'))->mapWithKeys(function ($quantity, $service) {
				return [$service => [
					'quantity' => $quantity
				]];
			})->filter(function ($item) {
				return $item['quantity'] > 0;
			});
			
			
			$this->application->services()->sync($services);
			
		}
	}
}
