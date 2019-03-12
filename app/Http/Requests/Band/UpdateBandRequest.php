<?php

namespace App\Http\Requests\Band;

use App\Models\Band;
use App\Models\Field;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBandRequest extends FormRequest {
	protected $band;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->band = $this->route('band');
		return $this->user()->can('update', $this->band);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		$rules = collect([
			'name' => 'required|min:2',
			'email' => 'required|email|unique:users,email,' . $this->band->user->id,
			'language' => 'required|in:en,nl',
			'band' => 'required|array',
			'paymentMethod' => 'required|string|in:band,individual'
		]);

		$fieldRules = Field::getRequiredFields(Band::class);
		$rules = $rules->merge($fieldRules);
		return $rules->toArray();
	}

	public function commit(){
		$this->band->user->name = $this->input('name');
		$this->band->user->email = $this->input('email');
		$this->band->user->language = $this->input('language');
		$this->band->user->save();

		$this->band->data = array_filter($this->input('band'));
		$this->band->payment_method = $this->input('paymentMethod');
		$this->band->save();
	}
}