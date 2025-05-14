<?php

namespace App\Http\Requests\Worker;

use App\Events\Worker\WorkerProfileFilled;
use App\Models\Field;
use App\Models\Worker;
use App\Models\WorkerApplication;
use Carbon\Carbon;
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
            'type' => 'required|in:0,1,2',
            'first_name' => 'required',
            'surname' => 'required'
		]);
		if ($this->input('review') || $this->worker->submitted) {
			$requiredFieldsRules = Field::getRequiredFields(Worker::class);
			$protectedFieldsRules = Field::getProtectedFields(Worker::class);
			$rules = $rules->merge($requiredFieldsRules)->merge($protectedFieldsRules)->merge(['liability' => 'accepted']);
        }
		return $rules->toArray();
	}

	public function withValidator($validator) {
		$validator->after(function ($validator) {
			if ($this->input('type') !== '1' && $this->input('review') && !$this->worker->photos()->count()) {
				$validator->errors()->add('photos', __('validation.required', ['attribute ' => 'photos ']));
			}
		});
	}

	public function commit() {
		$this->worker->user->name = $this->input('name');
		$this->worker->user->email = $this->input('email');
		$this->worker->user->language = $this->input('language');
        $this->worker->type = $this->input('type');
		$this->worker->user->save();
        $this->worker->first_name = $this->input('first_name');
        $this->worker->surname = $this->input('surname');
		$this->worker->data = $this->input('worker');
		if ($this->has('review') && !$this->worker->submitted) {
            $year = Carbon::today()->year;
			$this->worker->submitted = true;
            $this->worker->last_submitted = $year;
			event(new WorkerProfileFilled($this->worker));
            if (!$this->worker->applications()->where('year', $year)->exists()){
                $workerApp = new WorkerApplication();
                $workerApp->year = $year;
                $this->worker->applications()->save($workerApp);
            }
		}
		$this->worker->liability = $this->filled('liability');
		$this->worker->save();

	}
}
