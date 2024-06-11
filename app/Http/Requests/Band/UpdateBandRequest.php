<?php

namespace App\Http\Requests\Band;

use App\Models\Band;
use App\Models\BandAdmin;
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

		if ($this->input('review') || $this->band->submitted){
			$requiredFieldsRules = Field::getRequiredFields(Band::class);
			$protectedFieldsRules = Field::getProtectedFields(Band::class);
			$rules = $rules->merge($requiredFieldsRules)->merge($protectedFieldsRules);
		}
		return $rules->toArray();
	}
	
	public function withValidator($validator) {
		$validator->after(function ($validator) {
			if ($this->input('review') && !$this->band->bandSongs->count()) {
				$validator->errors()->add('tracks', __('bands/products.menuError'));
			}
		});
	}

	public function commit(){
		$this->band->user->name = $this->input('name');
		$this->band->user->email = $this->input('email');
		$this->band->user->language = $this->input('language');
		$this->band->user->save();
		if ($this->input('review') && !$this->band->submitted) {
			$this->band->submitted = true;
		}
		$this->band->data = array_filter($this->input('band'));
		$this->band->payment_method = $this->input('paymentMethod');
		if ($this->input('paymentMethod') == 'individual' && !$this->band->admin()->exists()){
			$this->addAdmin();
		}
		$this->band->save();
	}

	protected function addAdmin() {
		$bandAdmin = new BandAdmin;
		$bandAdmin->data = [];
		$this->band->admin()->save($bandAdmin);
	}
}
