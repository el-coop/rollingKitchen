<?php

namespace App\Http\Requests\Band\BandAdmin;

use App\Events\Band\BandAdminProfileFilled;
use App\Models\BandAdmin;
use App\Models\Field;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBandAdminRequest extends FormRequest {

	private $bandAdmin;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->bandAdmin = $this->route('bandAdmin');
		return $this->user()->can('update', $this->bandAdmin->band);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		$maxPayment = $this->bandAdmin->band->available_budget + $this->bandAdmin->payment;
		if ($maxPayment < 0) {
			$maxPayment = 0;
		}
		$rules = collect([
			'name' => 'required',
			'bandmember' => 'required|array',
			'payment' => 'required|numeric|min:0|max:' . $maxPayment,
		]);
		if ($this->input('review') || $this->bandAdmin->submitted) {
			$requiredFieldsRules = Field::getRequiredFields(BandAdmin::class);
			$protectedFieldsRules = Field::getProtectedFields(BandAdmin::class);
			$rules = $rules->merge($requiredFieldsRules)->merge($protectedFieldsRules);
		}
		return $rules->toArray();
	}

	public function withValidator($validator) {
		$validator->after(function ($validator) {
			if ($this->input('review') && !$this->bandAdmin->photos()->count()) {
				$validator->errors()->add('photos', __('validation.required', ['attribute ' => 'photos ']));
			}
		});
	}


	public function commit() {
		$this->bandAdmin->name = $this->input('name');
		$this->bandAdmin->data = array_filter($this->input('bandmember'));
		$this->bandAdmin->payment = $this->input('payment');
		if ($this->input('review') && !$this->bandAdmin->submitted) {
			$this->bandAdmin->submitted = true;
			event(new BandAdminProfileFilled($this->bandAdmin));
		}
		$this->bandAdmin->save();
	}
}
