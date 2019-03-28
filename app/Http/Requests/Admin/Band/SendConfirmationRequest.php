<?php

namespace App\Http\Requests\Admin\Band;

use App\Models\Band;
use App\Models\User;
use App\Notifications\Band\ConfirmationNotification;
use Illuminate\Foundation\Http\FormRequest;

class SendConfirmationRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return $this->user()->can('sendConfirmation', Band::class);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [

		];
	}

	public function commit(){
		$bands = Band::whereHas('schedules', function ($query){
			$query->where('approved', 'accepted');
		})->get();
		$bands->each(function ($band){
			$band->user->notify(new ConfirmationNotification());
		});
	}
}
