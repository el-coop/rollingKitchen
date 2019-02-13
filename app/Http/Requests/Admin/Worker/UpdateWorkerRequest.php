<?php

namespace App\Http\Requests\Admin\Worker;

use App\Models\Field;
use App\Models\Worker;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkerRequest extends FormRequest {
	protected $worker;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		
		$this->worker = $this->route('worker');
		return $this->user()->can('update', $this->worker);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			
			'name' => 'required',
			'email' => 'required|email|unique:users,email,' . $this->worker->user->id,
			'type' => 'required|in:0,1,2',
			'language' => 'required|in:en,nl',
			'supervisor' => 'boolean',
			'approved' => 'boolean',
			'worker' => 'required|array',
			'workplaces' => 'required|array',
			'workplaces.*' => 'required|exists:workplaces,id'
		];
		
	}
	
	public function commit() {
		
		$this->worker->user->name = $this->input('name');
		$this->worker->user->email = $this->input('email');
		$this->worker->type = $this->input('type');
		$this->worker->user->language = $this->input('language');
		$this->worker->supervisor = $this->filled('supervisor');
		$this->worker->approved = $this->filled('approved');
		
		$this->worker->data = array_filter($this->input('worker'));
		
		$this->worker->user->save();
		$this->worker->save();
		
		$this->worker->workplaces()->sync($this->input('workplaces'));
		
		
		return [
			'id' => $this->worker->id,
			'name' => $this->input('name'),
			'workplacesList' => $this->worker->workplacesList,
			'completed' => count($this->worker->data),
			'approved' => $this->worker->approved
		
		];
	}
}
