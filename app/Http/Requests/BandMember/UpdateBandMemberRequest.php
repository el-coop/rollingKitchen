<?php

namespace App\Http\Requests\BandMember;

use App\Events\BandMember\BandMemberProfileFilled;
use App\Models\BandMember;
use App\Models\Field;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBandMemberRequest extends FormRequest {

	private $bandMember;

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->bandMember = $this->route('bandMember');
		return $this->user()->can('update', $this->bandMember);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		$rules = collect([
			'name' => 'required',
			'email' => 'required|email|unique:users,email,' . $this->bandMember->user->id,
			'language' => 'required|in:en,nl',
			'bandmember' => 'required|array'
		]);
		if ($this->input('review') || $this->bandMember->submitted) {
			$requiredFieldsRules = Field::getRequiredFields(BandMember::class);
			$protectedFieldsRules = Field::getProtectedFields(BandMember::class);
			$rules = $rules->merge($requiredFieldsRules)->merge($protectedFieldsRules);
		}
		return $rules->toArray();
	}

	public function withValidator($validator) {
		$validator->after(function ($validator) {
			if ($this->input('review') && !$this->bandMember->photos()->count()) {
				$validator->errors()->add('photos', __('validation.required', ['attribute ' => 'photos ']));
			}
		});
	}


	public function commit() {
		$this->bandMember->user->name = $this->input('name');
		$this->bandMember->user->email = $this->input('email');
		$this->bandMember->user->language = $this->input('language');
		$this->bandMember->data = $this->input('bandmember');
		if ($this->input('review') && !$this->bandMember->submitted) {
			$this->bandMember->submitted = true;
			event(new BandMemberProfileFilled($this->bandMember));
		}
		$this->bandMember->save();
		$this->bandMember->user->save();
	}
}
