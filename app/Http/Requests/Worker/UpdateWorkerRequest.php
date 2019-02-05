<?php

namespace App\Http\Requests\Worker;

use App\Models\Field;
use App\Models\Worker;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkerRequest extends FormRequest {
	private $worker;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		
		$this->worker = $this->route('worker');
		return $this->user()->can('view', $this->worker);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		$rules = collect([
			'name' => 'required|min:2',
			'email' => 'required|email|unique:users,email,' . $this->worker->user->id,
			'language' => 'required|in:en,nl',
			'worker' => 'required|array',
		]);
		
		$fieldRules = Field::getRequiredFields(Worker::class);
		$rules = $rules->merge($fieldRules);
		return $rules->toArray();
	}
	
	public function commit() {
		$this->worker->user->name = $this->input('name');
		$this->worker->user->email = $this->input('email');
		$this->worker->user->language = $this->input('language');
		$this->worker->user->save();
		
		
		$this->worker->data = array_filter($this->input('worker'));
		$this->worker->save();
	}
}
