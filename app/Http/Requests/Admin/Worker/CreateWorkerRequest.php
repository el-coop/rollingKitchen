<?php

namespace App\Http\Requests\Admin\Worker;

use App\Models\User;
use App\Models\Worker;
use App\Models\Workplace;
use Illuminate\Foundation\Http\FormRequest;
use Password;

class CreateWorkerRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->can('create', Worker::class);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => 'required',
			'email' => 'required|email|unique:users',
			'type' => 'required|in:0,1,2',
			'language' => 'required|in:en,nl',
			'supervisor' => 'boolean',
			'workplaces' => 'required|array',
			'workplaces.*' => 'required|exists:workplaces,id',
            'first_name' => 'required',
            'surname' => 'required'
		];
	}

	public function commit() {
		$worker = new Worker;
		$user = new User;

		$user->email = $this->input('email');
		$user->name = $this->input('name');
		$user->language = $this->input('language');
		$user->password = '';
		$worker->supervisor = $this->filled('supervisor');
		$worker->type = $this->input('type');
        $worker->first_name = $this->input('first_name');
        $worker->surname = $this->input('surname');
		$worker->data = [];
		$worker->save();
		$worker->user()->save($user);

		$worker->workplaces()->attach($this->input('workplaces'));

		Password::broker()->sendResetLink(
			['email' => $user->email]
		);

		$worker->name = $user->name;

		return $worker->load('user');
	}
}
