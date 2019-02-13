<?php

namespace App\Http\Requests\Worker\Supervisor;

use App\Models\User;
use App\Models\Worker;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Password;

class CreateWorkerRequest extends FormRequest {

	protected $workplace;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->workplace = $this->route('workplace');
		return $this->user()->can('create', Worker::class) && $this->workplace->hasWorker($this->user()->user) && $this->user()->user->isSupervisor();
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
		];
	}

	public function commit(){
		$worker = new Worker;
		$user = new User;

		$user->email = $this->input('email');
		$user->name = $this->input('name');
		$user->language = $this->input('language');
		$user->password = '';
		$worker->supervisor = false;
		$worker->type = $this->input('type');
		$worker->data = [];
		$worker->save();
		$worker->user()->save($user);

		$worker->workplaces()->attach($this->workplace);

		Password::broker()->sendResetLink(
			['email' => $user->email]
		);

		$worker->name = $user->name;

		return $worker->load('user');
	}
}
