<?php

namespace App\Http\Requests\Band;

use App\Models\BandMember;
use App\Models\User;
use Password;
use Illuminate\Foundation\Http\FormRequest;

class CreateBandMemberRequest extends FormRequest {
	protected $band;
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->band = $this->route('band');
		return $this->user()->can('create', BandMember::class);
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
			'language' => 'required|in:en,nl'
		];
	}

	public function commit(){
		$bandMember = new BandMember;
		$user = new User;

		$user->email = $this->input('email');
		$user->name = $this->input('name');
		$user->language = $this->input('language');
		$user->password = '';
		$this->band->bandMembers()->save($bandMember);
		$bandMember->user()->save($user);
		Password::broker()->sendResetLink(
			['email' => $user->email]
		);

		return [
			'id' => $bandMember->id,
			'name' => $this->input('name'),
			'email' => $this->input('email')
		];
	}
}
